<?php

namespace App\Ninja\Repositories;

use App\Events\Setting\ItemTransferWasCreatedEvent;
use App\Events\Setting\ItemTransferWasUpdatedEvent;
use App\Models\ItemMovement;
use App\Models\ItemStore;
use App\Models\ItemTransfer;
use App\Models\Product;
use App\Models\Status;
use App\Models\Warehouse;
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
            ->withTrashed()->where('is_deleted', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_transfers')
            ->LeftJoin('accounts', 'accounts.id', '=', 'item_transfers.account_id')
            ->LeftJoin('users', 'users.id', '=', 'item_transfers.user_id')
            ->LeftJoin('products', 'products.id', '=', 'item_transfers.product_id')
            ->LeftJoin('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
            ->LeftJoin('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->LeftJoin('warehouses as previousWarehouse', 'previousWarehouse.id', '=', 'item_transfers.previous_warehouse_id')
            ->LeftJoin('warehouses as currentWarehouse', 'currentWarehouse.id', '=', 'item_transfers.current_warehouse_id')
            ->LeftJoin('statuses', 'statuses.id', '=', 'item_transfers.status_id')
            ->where('item_transfers.account_id', $accountId)
//->where('item_transfers.deleted_at', null)
            ->select(
                'item_transfers.id',
                'item_transfers.public_id',
                'item_transfers.user_id',
                'item_transfers.product_id',
                'item_transfers.previous_warehouse_id',
                'item_transfers.current_warehouse_id',
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
                'previousWarehouse.name as from_store_name',
                'previousWarehouse.public_id as from_store_public_id',
                'currentWarehouse.name as to_store_name',
                'currentWarehouse.public_id as to_store_public_id',
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
                    ->orWhere('currentWarehouse.name', 'like', '%' . $filter . '%');
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

        $query = $this->find()->where('item_transfers.product_id', $productId);

        return $query;
    }

    public function findWarehouse($warehousePublicId)
    {
        if (empty($warehousePublicId)) {
            return;
        }
        $warehouseId = Warehouse::getPrivateId($warehousePublicId);

        $query = $this->find()->where('item_transfers.warehouse_id', $warehouseId);

        return $query;
    }

    public function findStatus($statusPublicId)
    {
        if (empty($statusPublicId)) {
            return;
        }
        $statusId = Status::getPrivateId($statusPublicId);

        $query = $this->find()->where('item_transfers.warehouse_id', $statusId);

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
        $newQty = isset($data['qty']) ? Utils::parseFloat($data['qty']) : 0;

        $previous_warehouse = ItemStore::where('warehouse_id', $data['previous_warehouse_id'])
            ->whereIn('product_id', $data['product_id'])->get();

        if (count($previous_warehouse)) {
            $itemTransferDate = [];
            foreach ($previous_warehouse as $itemWarehouse) {
                $qoh = Utils::parseFloat($itemWarehouse->qty);
                $itemTransfer = $this->getInstanceOfItemTransfer($data, $update);
                $itemTransfer->product_id = $itemWarehouse->product_id;
                if (!empty($data['transfer_all_item'])) {
                    $itemTransfer->qty = $qoh;
                    $itemTransferDate['qty'] = 0;
                    if ($itemWarehouse->update($itemTransferDate)) {
                        $this->updateCurrentWarehouse($qoh, $data, $itemWarehouse);
                        $itemTransfer->save();
                    }
                } else {
                    if (empty($data['qty'])) {
                        continue;
                    } else {
                        if ($newQty >= $qoh) {
                            $itemTransferDate['qty'] = 0;
                            if ($itemWarehouse->update($itemTransferDate)) {
                                $this->updateCurrentWarehouse($qoh, $data, $itemWarehouse);
                                $itemTransfer->save();
                            }
                        } else {
                            $qoh = $qoh - $newQty;
                            $itemTransferDate['qty'] = $qoh;
                            if ($itemWarehouse->update($itemTransferDate)) {
                                $this->updateCurrentWarehouse($newQty, $data, $itemWarehouse);
                                $itemTransfer->save();
                            }
                        }
                    }
                }
            }
        }

        if ($update) {
            event(new ItemTransferWasUpdatedEvent($itemTransfer));
        } else {
            event(new ItemTransferWasCreatedEvent($itemTransfer));
        }
    }

    public function updateCurrentWarehouse($transferQty, $data, $itemTransfer = null)
    {
        $newQty = isset($transferQty) ? Utils::parseFloat($transferQty) : 0;

        $current_warehouse = ItemStore::where('warehouse_id', $data['current_warehouse_id'])
            ->where('product_id', $itemTransfer->product_id)->first();

        if (empty($current_warehouse)) {
            $current_warehouse = ItemStore::createNew();
            $current_warehouse->bin = $itemTransfer->bin;
            $current_warehouse->product_id = $itemTransfer->product_id;
            $current_warehouse->warehouse_id = $data['current_warehouse_id'];
            $current_warehouse->created_by = auth()->user()->username;
            $current_warehouse->qty = $newQty;
            $current_warehouse->notes = 'stock transfer';
            $current_warehouse->created_by = auth()->user()->username;
            $current_warehouse->save();
        } else {
            $qoh = isset($current_warehouse) ? Utils::parseFloat($current_warehouse->qty) : 0;
            $current_data['qty'] = $qoh + $newQty;
            $current_data['notes'] = 'stock transfer';
            $current_data['updated_by'] = auth()->user()->username;
            $current_warehouse->update($current_data);
        }
    }

    public function getInstanceOfItemTransfer($data, $update = null)
    {
        if ($update) {
            $itemTransfer = ItemTransfer::createNew();
            $itemTransfer->dispatch_date = Carbon::now();
            $itemTransfer->updated_by = auth()->user()->username;
            $itemTransfer->fill(collect($data)->except(['product_id'])->toArray());
        } else {
            $itemTransfer = ItemTransfer::createNew();
            $itemTransfer->dispatch_date = Carbon::now();
            $itemTransfer->fill(collect($data)->except(['product_id'])->toArray());
            $itemTransfer->created_by = auth()->user()->username;

        }

        return $itemTransfer;
    }

//    public function stockMovement($data, $itemTransfer = null, $update = false)
//    {
//        if (empty($data['qty'])) {
//            return;
//        }
//
//        $newQty = isset($data['qty']) ? Utils::parseFloat($data['qty']) : 0;
//        if ($update) {
//            $qoh = Utils::parseFloat($itemTransfer->qty);
//            $movable = ItemMovement::createNew();
//            $itemTransfer = $this->itemTransfer
//                ->where('warehouse_id', $itemTransfer->current_warehouse_id)
//                ->where('product_id', $itemTransfer->product_id)->first();
//            $movable->qty = $newQty;
//            $movable->qoh = $qoh + $newQty;
//            $movable->notes = 'stock transfer';
//            $movable->updated_by = auth()->user()->username;
//            $itemTransfer->stockMovements()->save($movable);
//        } else {
//            $movable = ItemMovement::createNew();
//            $movable->qty = $newQty;
//            $movable->qoh = $newQty;
//            $movable->notes = 'stock transfer';
//            $movable->updated_by = auth()->user()->username;
//            $itemTransfer->stockMovements()->save($movable);
//        }
//    }

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