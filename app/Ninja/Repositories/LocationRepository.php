<?php

namespace App\Ninja\Repositories;

use App\Events\LocationWasCreated;
use App\Events\LocationWasUpdated;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class LocationRepository extends BaseRepository
{
    private $model;

    public function __construct(Location $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Location';
    }

    public function all()
    {
        return Location::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('locations')
            ->where('locations.account_id', '=', $accountId)
//            ->where('locations.deleted_at', '=', null)
            ->select(
                'locations.id',
                'locations.public_id',
                'locations.name as location_name',
                'locations.is_deleted',
                'locations.notes',
                'locations.created_at',
                'locations.updated_at',
                'locations.deleted_at',
                'locations.created_by',
                'locations.updated_by',
                'locations.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('locations.name', 'like', '%' . $filter . '%')
                    ->orWhere('locations.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_LOCATION);

        return $query;
    }

    public function save($data, $location = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($location) {
            $location->updated_by = auth::user()->username;
        } elseif ($publicId) {
            $location = Location::scope($publicId)->withArchived()->firstOrFail();
            Log::warning('Entity not set in location repo save');
        } else {
            $location = Location::createNew();
            $location->created_by = auth::user()->username;
        }
        $location->fill($data);
        $location->name = isset($data['name']) ? trim($data['name']) : '';
        $location->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $location->save();

        if ($publicId) {
            event(new LocationWasUpdated($location, $data));
        } else {
            event(new LocationWasCreated($location, $data));
        }
        return $location;
    }

    public function findPhonetically($locationName)
    {
        $locationNameMeta = metaphone($locationName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $locationId = 0;
        $locations = Location::scope()->get();
        if (!empty($locations)) {
            foreach ($locations as $location) {
                if (!$location->name) {
                    continue;
                }
                $map[$location->id] = $location;
                $similar = similar_text($locationNameMeta, metaphone($location->name), $percent);
                if ($percent > $max) {
                    $locationId = $location->id;
                    $max = $percent;
                }
            }
        }

        return ($locationId && isset($map[$locationId])) ? $map[$locationId] : null;
    }
}
