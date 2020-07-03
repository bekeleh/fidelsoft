<?php

namespace App\Ninja\Repositories;

use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitRepository extends BaseRepository
{
    private $model;

    public function __construct(Unit $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Unit';
    }

    public function all()
    {
        return Unit::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('units')
        ->leftJoin('accounts', 'accounts.id', '=', 'units.account_id')
        ->leftJoin('users', 'users.id', '=', 'units.user_id')
        ->where('units.account_id', '=', $accountId)
            //->where('units.deleted_at', '=', null)
        ->select(
            'units.id',
            'units.public_id',
            'units.name as unit_name',
            'units.is_deleted',
            'units.notes',
            'units.created_at',
            'units.updated_at',
            'units.deleted_at',
            'units.created_by',
            'units.updated_by',
            'units.deleted_by'
        );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('units.name', 'like', '%' . $filter . '%')
                ->orWhere('units.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_UNIT);

        return $query;
    }

    public function save($data, $unit = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($unit) {
            $unit->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $unit = Unit::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $unit = Unit::createNew();
            $unit->created_by = Auth::user()->username;
        }

        $unit->fill($data);
        $unit->name = isset($data['name']) ? trim($data['name']) : '';
        $unit->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $unit->save();

        return $unit;
    }

    public function findPhonetically($unitName)
    {
        $unitNameMeta = metaphone($unitName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $unitId = 0;
        $units = Unit::scope()->get();
        if (!empty($units)) {
            foreach ($units as $unit) {
                if (!$unit->name) {
                    continue;
                }
                $map[$unit->id] = $unit;
                $similar = similar_text($unitNameMeta, metaphone($unit->name), $percent);
                if ($percent > $max) {
                    $unitId = $unit->id;
                    $max = $percent;
                }
            }
        }

        return ($unitId && isset($map[$unitId])) ? $map[$unitId] : null;
    }
}