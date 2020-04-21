<?php

namespace App\Ninja\Repositories;

use App\Events\ItemTransferWasCreated;
use App\Events\ItemTransferWasUpdated;
use App\Models\ItemMovement;
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
        $query = DB::table('item_stores')
            ->join('accounts', 'accounts.id', '=', 'item_stores.account_id')
            ->join('products', 'products.id', '=', 'item_stores.product_id')
            ->join('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
            ->join('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->join('stores', 'stores.id', '=', 'item_stores.store_id')
            ->where('item_stores.account_id', '=', $accountId)
            //->where('item_stores.deleted_at', '=', null)
            ->select(
                'item_stores.id',
                'item_stores.public_id',
                'item_stores.product_id',
                'item_stores.store_id',
                'item_stores.bin',
                'item_stores.qty',
                'item_stores.reorder_level',
                'item_stores.EOQ',
                'item_stores.is_deleted',
                'item_stores.notes',
                'item_stores.created_at',
                'item_stores.updated_at',
                'item_stores.deleted_at',
                'item_stores.created_by',
                'item_stores.updated_by',
                'item_stores.deleted_by',
                'products.name as item_name',
                'item_brands.name as item_brand_name',
                'item_categories.name as item_category_name',
                'stores.name as store_name'
            );
        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('item_stores.notes', 'like', '%' . $filter . '%')
                    ->orWhere('item_stores.created_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_stores.updated_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_brands.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_categories.name', 'like', '%' . $filter . '%')
                    ->orWhere('products.name', 'like', '%' . $filter . '%')
                    ->orWhere('stores.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_STORE);

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

        $query = $this->find()->where('item_stores.store_id', '=', $storeId);

        return $query;
    }

    public function save($data, $itemTransfer = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemTransfer) {
//          quantity adjustment
            $this->quantityAdjustment($data, $itemTransfer, $update = true);
            $itemTransfer->fill(collect($data)->except('qty')->toArray());
            $itemTransfer->qty = isset($data['qty']) ? $data['qty'] + $itemTransfer->qty : '';
            $itemTransfer->updated_by = Auth::user()->username;
            $itemTransfer->save();
        } elseif ($publicId) {
            $itemTransfer = ItemTransfer::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in item store repo save');
        } else {
            $itemTransfer = ItemTransfer::createNew();
            $itemTransfer->fill($data);
            $itemTransfer->qty = isset($data['qty']) ? trim($data['qty']) : '';
            $itemTransfer->created_by = Auth::user()->username;

            if ($itemTransfer->save()) {
                $this->quantityAdjustment($data, $itemTransfer, $update = false);
            }
        }

        if ($publicId) {
            event(new ItemTransferWasUpdated($itemTransfer, $data));
        } else {
            event(new ItemTransferWasCreated($itemTransfer, $data));
        }
        return $itemTransfer;
    }

    public function quantityAdjustment($data, $itemTransfer = null, $update = false)
    {
        if ($update) {
//         update quantity
            $this->qoh = (int)$itemTransfer->qty;
            if (!empty($data['qty'])) {
                if ((int)$data['qty'] > 0) {
                    $movable = ItemMovement::createNew();
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