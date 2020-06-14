<?php

namespace App\Ninja\Repositories;

use App\Events\ItemTransferWasCreated;
use App\Events\ItemTransferWasUpdated;
use App\Models\ItemMovement;
use App\Models\ItemStore;
use App\Models\ItemTransfer;
use App\Models\Product;
use App\Models\Status;
use App\Models\Store;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ItemTransferRepository extends BaseRepository
{
    private $model;

    public function __construct(ItemTransfer $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ItemTransfer';
    }

    public function all()
    {
        return ItemTransfer::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }


    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_transfers')
            ->join('accounts', 'accounts.id', '=', 'item_transfers.account_id')
            ->join('users', 'users.id', '=', 'item_transfers.approver_id')
            ->join('products', 'products.id', '=', 'item_transfers.product_id')
            ->join('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
            ->join('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->join('stores as previousStore', 'previousStore.id', '=', 'item_transfers.previous_store_id')
            ->join('stores as currentStore', 'currentStore.id', '=', 'item_transfers.current_store_id')
            ->join('statuses', 'statuses.id', '=', 'item_transfers.status_id')
            ->where('item_transfers.account_id', '=', $accountId)
            //->where('item_transfers.deleted_at', '=', null)
            ->select(
                'item_transfers.id',
                'item_transfers.public_id',
                'item_transfers.user_id',
                'item_transfers.product_id',
                'item_transfers.previous_store_id',
                'item_transfers.current_store_id',
                'item_transfers.status_id',
                'item_transfers.approver_id',
                'item_transfers.qty',
                'item_transfers.is_deleted',
                'item_transfers.notes',
                'item_transfers.dispatch_date',
                'item_transfers.created_at',
                'item_transfers.updated_at',
                'item_transfers.deleted_at',
                'item_transfers.created_by',
                'item_transfers.updated_by',
                'item_transfers.deleted_by',
                'products.name as item_name',
                'products.public_id as product_public_id',
                'item_brands.name as item_brand_name',
                'item_brands.public_id as item_brand_public_id',
                'item_categories.name as item_category_name',
                'item_categories.public_id as item_category_public_id',
                'previousStore.name as from_store_name',
                'previousStore.public_id as from_store_public_id',
                'currentStore.name as to_store_name',
                'currentStore.public_id as to_store_public_id',
                'users.username as approver_name',
                'users.public_id as approver_public_id',
                'statuses.name as status_name',
                'statuses.public_id as status_public_id'
            );
        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('item_transfers.notes', 'like', '%' . $filter . '%')
                    ->orWhere('item_transfers.created_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_transfers.updated_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_brands.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_categories.name', 'like', '%' . $filter . '%')
                    ->orWhere('users.username', 'like', '%' . $filter . '%')
                    ->orWhere('products.name', 'like', '%' . $filter . '%')
                    ->orWhere('currentStore.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_TRANSFER);

        return $query;
    }

    public function findProduct($productPublicId)
    {
        if (!$productPublicId) {
            return null;
        }
        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('item_requests.product_id', '=', $productId);

        return $query;
    }

    public function findStore($storePublicId)
    {
        if (!$storePublicId) {
            return null;
        }
        $storeId = Store::getPrivateId($storePublicId);

        $query = $this->find()->where('item_transfers.store_id', '=', $storeId);

        return $query;
    }

    public function findStatus($statusPublicId)
    {
        if (!$statusPublicId) {
            return null;
        }
        $statusId = Status::getPrivateId($statusPublicId);

        $query = $this->find()->where('item_transfers.store_id', '=', $statusId);

        return $query;
    }

    public function save($data, $itemTransfer = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        $queryResult = false;
        if ($itemTransfer) {
            $queryResult = $this->storeQuantityAdjustment($data, $itemTransfer, $update = true);
        } elseif ($publicId) {
            $queryResult = ItemTransfer::scope($publicId)->withArchived()->firstOrFail();
            Log::warning('Entity not set in item transfer repo save');
        } else {
            $itemRepo = $this->storeQuantityAdjustment($data, $itemTransfer, $update = false);
        }

        return $queryResult;
    }

    public function storeQuantityAdjustment($itemTransferData, $itemTransfer = null, $update = null)
    {
        try {
            $itemTransfers = ItemStore::where('store_id', $itemTransferData['previous_store_id'])->whereIn('product_id', $itemTransferData['product_id'])->get();

            $itemTransferDate = [];
            foreach ($itemTransfers as $itemStore) {
                $itemTransfer = $this->getInstanceOfItemTransfer($itemTransferData, $itemTransfer, $update);
                $itemTransfer->product_id = $itemStore->product_id;
                if (!empty($itemTransferData['transfer_all_item'])) {
                    $itemTransfer->qty = $itemStore->qty;
                    $itemTransferDate['qty'] = 0;
                    if ($itemStore->update($itemTransferDate)) {
                        $itemTransfer->save();
                    }
                } else {
                    $requiredQty = (int)$itemTransferData['qty'];
                    $availableQty = (int)$itemStore->qty;
                    if ($requiredQty >= $availableQty) {
                        $itemTransferDate['qty'] = 0;
                        if ($itemStore->update($itemTransferDate)) {
                            $itemTransfer->save();
                        }
                    } else {
                        $availableQty = $availableQty - $requiredQty;
                        $itemTransferDate['qty'] = $availableQty;
                        if ($itemStore->update($itemTransferDate)) {
                            $itemTransfer->save();
                        }
                    }
                }
                if ($update) {
                    event(new ItemTransferWasUpdated($itemTransfer, $itemTransferData));
                } else {
                    event(new ItemTransferWasCreated($itemTransfer, $itemTransferData));
                }
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getInstanceOfItemTransfer($data, $itemTransfer, $update = null)
    {
        if ($update) {
            $itemTransfer = ItemTransfer::createNew();
            $itemTransfer->dispatch_date = Carbon::now();
            $itemTransfer->updated_by = Auth::user()->username;
            $itemTransfer->fill(collect($data)->except(['product_id'])->toArray());
        } else {
            $itemTransfer = ItemTransfer::createNew();
            $itemTransfer->dispatch_date = Carbon::now();
            $itemTransfer->fill(collect($data)->except(['product_id'])->toArray());
            $itemTransfer->created_by = Auth::user()->username;

        }
        return $itemTransfer;
    }

    public function quantityAdjustment($data, $itemTransfer = null, $update = false)
    {
        if ($update) {

            $this->qoh = (int)$itemTransfer->qty;
            if (!empty($data['qty'])) {
                if ((int)$data['qty'] > 0) {
                    $movable = ItemMovement::createNew();
                    $itemTransfer = $this->itemTransfer->where('store_id', $itemTransfer->current_store_id)->where('product_id', $itemTransfer->product_id)->first();
                    $movable->qty = (int)$data['qty'];
                    $movable->qoh = ((int)$this->qoh) + ((int)$data['qty']);
                    $movable->notes = 'quantity adjustment';
                    $movable->updated_by = auth::user()->username;
                    $itemTransfer->itemMovements()->save($movable);
                }
            }
        } else {
//           create new quantity
            if (!empty($data['qty'])) {
                if ((int)$data['qty'] > 0) {
                    $movable = ItemMovement::createNew();
                    $movable->qty = (int)$data['qty'];
                    $movable->qoh = (int)$data['qty'];
                    $movable->notes = 'quantity adjustment';
                    $movable->updated_by = auth::user()->username;
                    $itemTransfer->itemMovements()->save($movable);
                }
            }
        }
    }

    public function findPhonetically($itemTransferName)
    {
        $itemTransferNameMeta = metaphone($itemTransferName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemTransferId = 0;
        $itemTransfers = ItemTransfer::scope()->get();
        if (!empty($itemTransfers)) {
            foreach ($itemTransfers as $itemTransfer) {
                if (!$itemTransfer->bin) {
                    continue;
                }
                $map[$itemTransfer->id] = $itemTransfer;
                $similar = similar_text($itemTransferNameMeta, metaphone($itemTransfer->bin), $percent);
                if ($percent > $max) {
                    $itemTransferId = $itemTransfer->id;
                    $max = $percent;
                }
            }
        }

        return ($itemTransferId && isset($map[$itemTransferId])) ? $map[$itemTransferId] : null;
    }
}