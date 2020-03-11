<?php

namespace App\Ninja\Repositories;

use App\Events\StoreWasCreated;
use App\Events\StoreWasUpdated;
use App\Models\Location;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class StoreRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\Store';
    }

    public function all()
    {
        return Store::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('stores')->join('locations', 'locations.id', '=', 'stores.location_id')
            ->where('stores.account_id', '=', $accountId)->select('stores.*');
        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('stores.name', 'like', '%' . $filter . '%')
                    ->orWhere('stores.notes', 'like', '%' . $filter . '%')
                    ->orWhere('stores.store_code', 'like', '%' . $filter . '%')
                    ->orWhere('locations.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_STORE);

        return $query;
    }

    public function findLocation($locationPublicId)
    {
        $locationId = Location::getPrivateId($locationPublicId);

        $query = $this->find()->where('locations.location_id', '=', $locationId);

        return $query;
    }

    public function save($data, $store = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($store) {
            // do nothing
        } elseif ($publicId) {
            $store = Store::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in store repo save');
        } else {
            $store = Store::createNew();
        }
        $store->fill($data);
        $store->name = isset($data['name']) ? trim($data['name']) : '';
        $store->store_code = isset($data['store_code']) ? trim($data['store_code']) : '';
        $store->location_id = isset($data['location_id']) ? trim($data['location_id']) : '';
        $store->notes = isset($data['notes']) ? trim($data['notes']) : '';
//      save the data
        $store->save();

        if ($publicId) {
            event(new StoreWasUpdated($store, $data));
        } else {
            event(new StoreWasCreated($store, $data));
        }
        return $store;
    }

    public function findPhonetically($storeName)
    {
        $storeNameMeta = metaphone($storeName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $storeId = 0;
        $stores = Store::scope()->get();
        if (!empty($stores)) {
            foreach ($stores as $store) {
                if (!$store->name) {
                    continue;
                }
                $map[$store->id] = $store;
                $similar = similar_text($storeNameMeta, metaphone($store->name), $percent);
                if ($percent > $max) {
                    $storeId = $store->id;
                    $max = $percent;
                }
            }
        }

        return ($storeId && isset($map[$storeId])) ? $map[$storeId] : null;
    }
}