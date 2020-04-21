<?php

namespace App\Ninja\Repositories;

use App\Events\ItemTransferWasCreated;
use App\Events\ItemTransferWasUpdated;
use App\Models\ItemMovement;
use App\Models\ItemStore;
use App\Models\ItemTransfer;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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


    public function find($accountId, $filter = null)
    {
        $query = DB::table('item_transfers')
            ->join('accounts', 'accounts.id', '=', 'item_transfers.account_id')
            ->join('users', 'users.id', '=', 'item_transfers.approver_id')
            ->join('products', 'products.id', '=', 'item_transfers.product_id')
            ->join('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
            ->join('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->join('stores as currentStore', 'currentStore.id', '=', 'item_transfers.current_store_id')
            ->join('approval_statuses', 'approval_statuses.id', '=', 'item_transfers.approval_status_id')
            ->where('item_transfers.account_id', '=', $accountId)
            //->where('item_transfers.deleted_at', '=', null)
            ->select(
                'item_transfers.id',
                'item_transfers.public_id',
                'item_transfers.product_id',
                'item_transfers.previous_store_id',
                'item_transfers.current_store_id',
                'item_transfers.approval_status_id',
                'item_transfers.approver_id',
                'item_transfers.qty',
                'item_transfers.is_deleted',
                'item_transfers.notes',
                'item_transfers.approved_date',
                'item_transfers.created_at',
                'item_transfers.updated_at',
                'item_transfers.deleted_at',
                'item_transfers.created_by',
                'item_transfers.updated_by',
                'item_transfers.deleted_by',
                'products.name as item_name',
                'item_brands.name as item_brand_name',
                'item_categories.name as item_category_name',
                'stores.name as to_store_name',
                'users.username as approver_name'
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
                    ->orWhere('stores.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_TRANSFER);

        return $query;
    }

    public function findProduct($productPublicId)
    {
        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('products.product_id', '=', $productId);

        return $query;
    }

    public function findStore($storePublicId)
    {
        $storeId = Store::getPrivateId($storePublicId);

        $query = $this->find()->where('item_transfers.store_id', '=', $storeId);

        return $query;
    }

    public function save($data, $itemTransfer = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemTransfer) {
            $itemTransfer->updated_by = Auth::user()->username;
            $itemTransfer->fill(collect($data)->except(['item_id'])->toArray());
            $this->storeQuantityAdjustment($data, $itemTransfer, $update = true);
            dd($itemTransfer);
        } elseif ($publicId) {
            $itemTransfer = ItemTransfer::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in item transfer repo save');
        } else {
            $itemTransfer = ItemTransfer::createNew();
            $itemTransfer->fill(collect($data)->except(['item_id'])->toArray());
            $itemTransfer->created_by = Auth::user()->username;
            $this->storeQuantityAdjustment($data, $itemTransfer, $update = false);
        }

        if ($publicId) {
            event(new ItemTransferWasUpdated($itemTransfer, $data));
        } else {
            event(new ItemTransferWasCreated($itemTransfer, $data));
        }
        return $itemTransfer;
    }

    public function storeQuantityAdjustment($itemTransferData, $itemTransfer = null, $update = false)
    {
        try {
            $ItemId = Store::getPrivateId($itemTransferData['item_id']);
            $PreviousStoreId = Store::getPrivateId($itemTransferData['previous_store_id']);

            $itemTransfers = ItemStore::where('store_id', $itemTransferData['previous_store_id'])->whereIn('item_id', $itemTransferData['item_id'])->get();
            $itemTransferDate = [];
            foreach ($itemTransfers as $itemStore) {
                $itemTransfer->item_id = $itemStore->item_id;
                if ((int)$itemTransfer->qty > 0) {
                    if (!empty($itemTransferData['transferAllQtyChecked'])) {
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
                            if ($itemTransfer->update($itemTransferDate)) {
                                $itemTransfer->save();
                            }
                        }
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function quantityAdjustment($data, $itemTransfer = null, $update = false)
    {
        if ($update) {

            $this->qoh = (int)$itemTransfer->qty;
            if (!empty($data['qty'])) {
                if ((int)$data['qty'] > 0) {
                    $movable = ItemMovement::createNew();
                    $itemTransfer = $this->itemTransfer->where('store_id', $itemTransfer->current_store_id)->where('item_id', $itemTransfer->item_id)->first();
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