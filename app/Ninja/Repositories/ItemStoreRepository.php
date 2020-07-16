<?php

namespace App\Ninja\Repositories;

use App\Events\ItemStoreWasCreated;
use App\Events\ItemStoreWasUpdated;
use App\Models\ItemMovement;
use App\Models\ItemStore;
use App\Models\Product;
use App\Models\Store;
use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ItemStoreRepository extends BaseRepository
{
    private $model;

    public function __construct(ItemStore $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ItemStore';
    }

    public function all()
    {
        return ItemStore::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }


    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_stores')
        ->leftJoin('accounts', 'accounts.id', '=', 'item_stores.account_id')
        ->leftJoin('users', 'users.id', '=', 'item_stores.user_id')
        ->leftJoin('products', 'products.id', '=', 'item_stores.product_id')
        ->leftJoin('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
        ->leftJoin('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
        ->leftJoin('warehouses', 'warehouses.id', '=', 'item_stores.store_id')
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
            'products.public_id as product_public_id',
            'products.product_key',
            'item_brands.public_id as item_brand_public_id',
            'item_brands.name as item_brand_name',
            'item_categories.public_id as item_category_public_id',
            'item_categories.name as item_category_name',
            'warehouses.public_id as store_public_id',
            'warehouses.name as store_name'
        );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('item_brands.name', 'like', '%' . $filter . '%')
                ->orWhere('item_categories.name', 'like', '%' . $filter . '%')
                ->orWhere('products.product_key', 'like', '%' . $filter . '%')
                ->orWhere('warehouses.name', 'like', '%' . $filter . '%')
                ->orWhere('item_stores.notes', 'like', '%' . $filter . '%')
                ->orWhere('item_stores.bin', 'like', '%' . $filter . '%')
                ->orWhere('item_stores.qty', 'like', '%' . $filter . '%')
                ->orWhere('item_stores.created_by', 'like', '%' . $filter . '%')
                ->orWhere('item_stores.updated_by', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_STORE);

        return $query;
    }

    public function getItems($accountId, $filter = null)
    {
        $query = DB::table('item_stores')
        ->leftJoin('accounts', 'accounts.id', '=', 'item_stores.account_id')
        ->leftJoin('users', 'users.id', '=', 'item_stores.user_id')
        ->leftJoin('products', 'products.id', '=', 'item_stores.product_id')
        ->leftJoin('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
        ->leftJoin('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
        ->leftJoin('warehouses', 'warehouses.id', '=', 'item_stores.store_id')
        ->Where('item_stores.store_id', '=', $filter)
        ->where('item_stores.account_id', '=', $accountId)
        ->where('item_stores.qty', '>', 0)
            //->where('item_stores.deleted_at', '=', null)
        ->select(
            'products.id as id',
            'products.product_key as name',
            'item_brands.name as item_brand_name',
            'item_categories.name as item_category_name'
        )->get();

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

    public function save($data, $itemStore = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        $newQty = isset($data['new_qty']) ? Utils::parseFloat($data['new_qty']):0;
        if ($itemStore) {
            $qoh = Utils::parseFloat($itemStore->qty);
            $itemStore->fill($data);
            $itemStore->qty = ($qoh +$newQty);
            $itemStore->updated_by = Auth::user()->username;
            if ($itemStore->save()) {
                $this->stockAdjustment($data, $itemStore, $update = true);
            }
        } elseif ($publicId) {
            $itemStore = ItemStore::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $itemStore = ItemStore::createNew();
            $itemStore->fill($data);
            $itemStore->qty = $newQty;
            $itemStore->created_by = Auth::user()->username;

            if ($itemStore->save()) {
                $this->stockAdjustment($data, $itemStore, $update = false);
            }
        }

        if ($publicId) {
            event(new ItemStoreWasUpdated($itemStore));
        } else {
            event(new ItemStoreWasCreated($itemStore));
        }

        return $itemStore;
    }

    public function stockAdjustment($data, $itemStore = null, $update = false)
    {
        if(empty($data['new_qty']) || empty($itemStore)){
            return;
        }
        
        $qoh = Utils::parseFloat($itemStore->qty);
        $newQty = Utils::parseFloat($data['new_qty']);
        if ($update) {
            $movable = ItemMovement::createNew();
            $movable->qty = $newQty;
            $movable->qoh = $qoh+ $newQty;
            $movable->notes = 'stock adjustment';
            $movable->updated_by = auth::user()->username;
            $itemStore->stockMovements()->save($movable);
        } else {
            $movable = ItemMovement::createNew();
            $movable->qty =  $qoh;
            $movable->qoh = $newQty ;
            $movable->notes = 'stock adjustment';
            $movable->updated_by = auth::user()->username;
            $itemStore->stockMovements()->save($movable);
        }
    }

    public function findPhonetically($itemStoreName)
    {
        $itemStoreNameMeta = metaphone($itemStoreName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemStoreId = 0;
        $itemStores = ItemStore::scope()->get();
        if (isset($itemStores)) {
            foreach ($itemStores as $itemStore) {
                if (!$itemStore->bin) {
                    continue;
                }
                $map[$itemStore->id] = $itemStore;
                $similar = similar_text($itemStoreNameMeta, metaphone($itemStore->bin), $percent);
                if ($percent > $max) {
                    $itemStoreId = $itemStore->id;
                    $max = $percent;
                }
            }
        }

        return ($itemStoreId && isset($map[$itemStoreId])) ? $map[$itemStoreId] : null;
    }
}