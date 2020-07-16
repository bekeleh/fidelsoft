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
use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        return ItemTransfer::scope()
        ->withTrashed()
        ->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_transfers')
        ->LeftJoin('accounts', 'accounts.id', '=', 'item_transfers.account_id')
        ->LeftJoin('users', 'users.id', '=', 'item_transfers.user_id')
        ->LeftJoin('products', 'products.id', '=', 'item_transfers.product_id')
        ->LeftJoin('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
        ->LeftJoin('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
        ->LeftJoin('warehouses as previousStore', 'previousStore.id', '=', 'item_transfers.previous_store_id')
        ->LeftJoin('warehouses as currentStore', 'currentStore.id', '=', 'item_transfers.current_store_id')
        ->LeftJoin('statuses', 'statuses.id', '=', 'item_transfers.status_id')
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
            'products.product_key',
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
                ->orWhere('products.product_key', 'like', '%' . $filter . '%')
                ->orWhere('currentStore.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_TRANSFER);

        return $query;
    }

    public function findProduct($productPublicId)
    {
        if (empty($productPublicId)) {
            return;
        }
        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('item_transfers.product_id', '=', $productId);

        return $query;
    }

    public function findStore($storePublicId)
    {
        if (empty($storePublicId)) {
            return;
        }
        $storeId = Store::getPrivateId($storePublicId);

        $query = $this->find()->where('item_transfers.store_id', '=', $storeId);

        return $query;
    }

    public function findStatus($statusPublicId)
    {
        if (empty($statusPublicId)) {
            return;
        }
        $statusId = Status::getPrivateId($statusPublicId);

        $query = $this->find()->where('item_transfers.store_id', '=', $statusId);

        return $query;
    }

    public function save($data, $itemTransfer = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemTransfer) {
            $this->stockAdjustment($data, $itemTransfer, $update = true);
        } elseif ($publicId) {
            ItemTransfer::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $this->stockAdjustment($data, $itemTransfer, $update = false);
        }

        return $itemTransfer;
    }

    public function stockAdjustment($data, $itemTransfer = null, $update = null)
    {
        if(empty($data['qty'])){
            return;
        }

        $newQty = isset($data['qty']) ? Utils::parseFloat($data['qty']):0;

        $itemTransfers = ItemStore::where('store_id', $data['previous_store_id'])
        ->whereIn('product_id', $data['product_id'])->get();
        if(count($itemTransfers)){
            $itemTransferDate = [];
            foreach ($itemTransfers as $itemStore) {
                $qoh = Utils::parseFloat($itemStore->qty);
                $itemTransfer = $this->getInstanceOfItemTransfer($data, $update);
                $itemTransfer->product_id = $itemStore->product_id;
                if (!empty($data['transfer_all_item'])) {
                    $itemTransfer->qty = $qoh;
                    $itemTransferDate['qty'] = 0;
                    if ($itemStore->update($itemTransferDate)) {
                        $itemTransfer->save();
                    }
                } else {
                    if ($newQty >= $qoh) {
                        $itemTransferDate['qty'] = 0;
                        if ($itemStore->update($itemTransferDate)) {
                            $itemTransfer->save();
                        }
                    } else {
                        $qoh = $qoh - $newQty;
                        $itemTransferDate['qty'] = $qoh;
                        if ($itemStore->update($itemTransferDate)) {
                            $itemTransfer->save();
                        }
                    }
                }
                if ($update) {
                    event(new ItemTransferWasUpdated($itemTransfer));
                } else {
                    event(new ItemTransferWasCreated($itemTransfer));
                }
            }
        }

        return $itemTransfer;
    }

    public function getInstanceOfItemTransfer($data, $update = null)
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

    public function stockMovement($data, $itemTransfer = null, $update = false)
    {
        if (empty($data['qty'])) {
            return;
        }

        $newQty = isset($data['qty']) ? Utils::parseFloat($data['qty']):0;
        if ($update) {
            $qoh = Utils::parseFloat($itemTransfer->qty);
            $movable = ItemMovement::createNew();
            $itemTransfer = $this->itemTransfer
            ->where('store_id', $itemTransfer->current_store_id)
            ->where('product_id', $itemTransfer->product_id)->first();
            $movable->qty = $newQty;
            $movable->qoh = $qoh + $newQty;
            $movable->notes = 'stock transfer';
            $movable->updated_by = auth::user()->username;
            $itemTransfer->stockMovements()->save($movable);
        } else {
            $movable = ItemMovement::createNew();
            $movable->qty = $newQty;
            $movable->qoh = $newQty;
            $movable->notes = 'stock transfer';
            $movable->updated_by = auth::user()->username;
            $itemTransfer->stockMovements()->save($movable);
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