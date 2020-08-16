<?php

namespace App\Ninja\Repositories;

use App\Events\WarehouseWasCreatedEvent;
use App\Events\WarehouseWasUpdatedEvent;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseRepository extends BaseRepository
{
    private $model;

    public function __construct(Warehouse $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Warehouse';
    }

    public function all()
    {
        return Warehouse::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('warehouses')
            ->leftJoin('accounts', 'accounts.id', '=', 'warehouses.account_id')
            ->leftJoin('users', 'users.id', '=', 'warehouses.user_id')
            ->leftJoin('locations', 'locations.id', '=', 'warehouses.location_id')
            ->where('warehouses.account_id', '=', $accountId)
            //->where('warehouses.deleted_at', '=', null)
            ->select(
                'warehouses.id',
                'warehouses.public_id',
                'warehouses.location_id',
                'warehouses.name as warehouse_name',
                'warehouses.is_deleted',
                'warehouses.notes',
                'warehouses.created_at',
                'warehouses.updated_at',
                'warehouses.deleted_at',
                'warehouses.created_by',
                'warehouses.updated_by',
                'warehouses.deleted_by',
                'locations.public_id as location_public_id',
                'locations.name as location_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('warehouses.name', 'like', '%' . $filter . '%')
                    ->orWhere('warehouses.notes', 'like', '%' . $filter . '%')
                    ->orWhere('locations.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_WAREHOUSE);

        return $query;
    }

    public function findLocation($locationPublicId)
    {
        $locationId = Location::getPrivateId($locationPublicId);

        $query = $this->find()->where('warehouses.location_id', '=', $locationId);

        return $query;
    }

    public function save($data, $Warehouse = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($Warehouse) {
            $Warehouse->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $Warehouse = Warehouse::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $Warehouse = Warehouse::createNew();
            $Warehouse->created_by = Auth::user()->username;
        }

        $Warehouse->fill($data);

        $Warehouse->name = isset($data['name']) ? trim($data['name']) : '';
        $Warehouse->location_id = isset($data['location_id']) ? trim($data['location_id']) : '';
        $Warehouse->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $Warehouse->save();

        if ($publicId) {
            event(new WarehouseWasUpdatedEvent($Warehouse));
        } else {
            event(new WarehouseWasCreatedEvent($Warehouse));
        }
        return $Warehouse;
    }

    public function findPhonetically($WarehouseName)
    {
        $WarehouseNameMeta = metaphone($WarehouseName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $WarehouseId = 0;
        $warehouses = Warehouse::scope()->get();
        if (!empty($warehouses)) {
            foreach ($warehouses as $Warehouse) {
                if (!$Warehouse->name) {
                    continue;
                }
                $map[$Warehouse->id] = $Warehouse;
                $similar = similar_text($WarehouseNameMeta, metaphone($Warehouse->name), $percent);
                if ($percent > $max) {
                    $WarehouseId = $Warehouse->id;
                    $max = $percent;
                }
            }
        }

        return ($WarehouseId && isset($map[$WarehouseId])) ? $map[$WarehouseId] : null;
    }
}