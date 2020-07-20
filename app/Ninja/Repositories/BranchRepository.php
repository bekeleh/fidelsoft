<?php

namespace App\Ninja\Repositories;

use App\Models\Branch;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class BranchRepository extends BaseRepository
{
    private $model;

    public function __construct(Branch $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Branch';
    }

    public function all()
    {
        return Branch::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('branches')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'branches.warehouse_id')
            ->leftJoin('locations', 'locations.id', '=', 'branches.location_id')
            ->where('branches.account_id', '=', $accountId)
            // ->whereNull('branches.deleted_at')
            ->select(
                'branches.id',
                'branches.public_id',
                'branches.name as branch_name',
                'branches.is_deleted',
                'branches.notes',
                'branches.created_at',
                'branches.updated_at',
                'branches.deleted_at',
                'branches.created_by',
                'branches.updated_by',
                'branches.deleted_by',
                'locations.public_id as location_public_id',
                'locations.name as location_name',
                'warehouses.public_id as warehouse_public_id',
                'warehouses.name as warehouse_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('branches.name', 'like', '%' . $filter . '%')
                    ->orWhere('branches.notes', 'like', '%' . $filter . '%')
                    ->orWhere('warehouses.name', 'like', '%' . $filter . '%')
                    ->orWhere('locations.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_BRANCH);

        return $query;
    }

    public function findLocation($locationPublicId)
    {
        $locationId = Location::getPrivateId($locationPublicId);

        $query = $this->find()->where('branches.location_id', '=', $locationId);

        return $query;
    }

    public function findWarehouse($warehousePublicId)
    {
        $warehouseId = Warehouse::getPrivateId($warehousePublicId);

        $query = $this->find()->where('branches.warehouse_id', '=', $warehouseId);

        return $query;
    }

    public function save($data, $branch = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($branch) {
            $branch->updated_by = auth::user()->username;
        } elseif ($publicId) {
            $branch = Branch::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $branch = Branch::createNew();
            $branch->created_by = auth::user()->username;
        }

        $branch->fill($data);
        $branch->name = isset($data['name']) ? trim($data['name']) : '';
        $branch->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $branch->save();


        return $branch;
    }

    public function findPhonetically($branchName)
    {
        $branchNameMeta = metaphone($branchName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $branchId = 0;
        $branches = Branch::scope()->get();
        if (!empty($branches)) {
            foreach ($branches as $branch) {
                if (!$branch->name) {
                    continue;
                }
                $map[$branch->id] = $branch;
                $similar = similar_text($branchNameMeta, metaphone($branch->name), $percent);
                if ($percent > $max) {
                    $branchId = $branch->id;
                    $max = $percent;
                }
            }
        }

        return ($branchId && isset($map[$branchId])) ? $map[$branchId] : null;
    }
}
