<?php

namespace App\Ninja\Repositories;

use App\Events\StoreWasCreated;
use App\Events\StoreWasUpdated;
use App\Models\Location;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StoreRepository extends BaseRepository
{
    private $model;

    public function __construct(Store $model)
    {
        $this->model = $model;
    }

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
        $query = DB::table('stores')
            ->join('accounts', 'accounts.id', '=', 'stores.account_id')
            ->join('locations', 'locations.id', '=', 'stores.location_id')
            ->where('stores.account_id', '=', $accountId)
            //->where('stores.deleted_at', '=', null)
            ->select(
                'stores.id',
                'stores.public_id',
                'stores.location_id',
                'stores.name as store_name',
                'stores.store_code',
                'stores.is_deleted',
                'stores.notes',
                'stores.created_at',
                'stores.updated_at',
                'stores.deleted_at',
                'stores.created_by',
                'stores.updated_by',
                'stores.deleted_by',
                'locations.name as location_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('stores.name', 'like', '%' . $filter . '%')
                    ->orWhere('stores.store_code', 'like', '%' . $filter . '%')
                    ->orWhere('stores.notes', 'like', '%' . $filter . '%')
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
            $store->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $store = Store::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in store repo save');
        } else {
            $store = Store::createNew();
            $store->created_by = Auth::user()->username;
        }
        $store->fill($data);
        $store->name = isset($data['name']) ? ucwords(trim($data['name'])) : '';
        $store->store_code = isset($data['store_code']) ? trim($data['store_code']) : '';
        $store->location_id = isset($data['location_id']) ? trim($data['location_id']) : '';
        $store->notes = isset($data['notes']) ? trim($data['notes']) : '';

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