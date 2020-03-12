<?php

namespace App\Ninja\Repositories;

use App\Events\LocationWasCreated;
use App\Events\LocationWasUpdated;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class LocationRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\Location';
    }

    public function all()
    {
        return Location::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('locations')->where('locations.account_id', '=', $accountId)->select('locations.*');

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('locations.name', 'like', '%' . $filter . '%')
                    ->orWhere('locations.notes', 'like', '%' . $filter . '%')
                    ->orWhere('locations.location_code', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_LOCATION);

        return $query;
    }

    public function save($data, $store = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($store) {
            // do nothing
        } elseif ($publicId) {
            $store = Location::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in location repo save');
        } else {
            $store = Location::createNew();
        }
        $store->fill($data);
        $store->name = isset($data['name']) ? trim($data['name']) : '';
        $store->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $store->save();

        if ($publicId) {
            event(new LocationWasUpdated($store, $data));
        } else {
            event(new LocationWasCreated($store, $data));
        }
        return $store;
    }

    public function findPhonetically($storeName)
    {
        $storeNameMeta = metaphone($storeName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $storeId = 0;
        $locations = Location::scope()->get();
        if (!empty($locations)) {
            foreach ($locations as $store) {
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
