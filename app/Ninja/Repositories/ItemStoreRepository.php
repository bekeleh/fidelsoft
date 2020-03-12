<?php

namespace App\Ninja\Repositories;

use App\Events\ItemStoreWasCreated;
use App\Events\ItemStoreWasUpdated;
use App\Models\ItemStore;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemStoreRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\ItemStore';
    }

    public function all()
    {
        return ItemStore::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }


    public function find($accountId, $filter = null)
    {
        $query = DB::table('item_stores')
            ->join('accounts', 'accounts.id', '=', 'item_stores.account_id')
            ->join('products', 'products.id', '=', 'item_stores.product_id')
            ->join('stores', 'stores.id', '=', 'item_stores.store_id')
            ->where('item_stores.account_id', '=', $accountId)
            //->where('item_stores.deleted_at', '=', null)
            ->select(
                'item_stores.id',
                'item_stores.public_id',
                'item_stores.product_id',
                'item_stores.store_id',
                'item_stores.bin',
                'item_stores.is_deleted',
                'item_stores.notes',
                'item_stores.created_at',
                'item_stores.updated_at',
                'item_stores.deleted_at',
                'item_stores.created_by',
                'item_stores.updated_by',
                'item_stores.deleted_by',
                'products.name as product_name',
                'stores.name as store_name'
            );

        $this->applyFilters($query, ENTITY_ITEM_STORE);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('item_stores.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_stores.store_code', 'like', '%' . $filter . '%')
                    ->orWhere('item_stores.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.name', 'like', '%' . $filter . '%')
                    ->orWhere('stores.name', 'like', '%' . $filter . '%');
            });
        }

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

        if ($itemStore) {
            $itemStore->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $itemStore = ItemStore::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in store repo save');
        } else {
            $itemStore = ItemStore::createNew();
            $itemStore->created_by = Auth::user()->username;
        }
        $itemStore->fill($data);
        $itemStore->bin = isset($data['name']) ? ucwords(trim($data['name'])) : '';
        $itemStore->qty = isset($data['qty']) ? trim($data['qty']) : '';
        $itemStore->product_id = isset($data['product_id']) ? trim($data['product_id']) : '';
        $itemStore->store_id = isset($data['store_id']) ? trim($data['store_id']) : '';
        $itemStore->notes = isset($data['notes']) ? trim($data['notes']) : '';
//      save the data
        $itemStore->save();

        if ($publicId) {
            event(new ItemStoreWasUpdated($itemStore, $data));
        } else {
            event(new ItemStoreWasCreated($itemStore, $data));
        }
        return $itemStore;
    }

    public function findPhonetically($itemStoreName)
    {
        $itemStoreNameMeta = metaphone($itemStoreName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemStoreId = 0;
        $itemStores = ItemStore::scope()->get();
        if (!empty($itemStores)) {
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