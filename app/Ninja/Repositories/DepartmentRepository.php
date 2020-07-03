<?php

namespace App\Ninja\Repositories;

use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentRepository extends BaseRepository
{
    private $model;

    public function __construct(Department $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Department';
    }

    public function all()
    {
        return Department::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('departments')
        ->where('departments.account_id', '=', $accountId)
        // ->whereNull('departments.deleted_at')
        ->select(
            'departments.id',
            'departments.public_id',
            'departments.name as department_name',
            'departments.is_deleted',
            'departments.notes',
            'departments.created_at',
            'departments.updated_at',
            'departments.deleted_at',
            'departments.created_by',
            'departments.updated_by',
            'departments.deleted_by'
        );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('departments.name', 'like', '%' . $filter . '%')
                ->orWhere('departments.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_DEPARTMENT);

        return $query;
    }

    public function save($data, $department = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($department) {
            $department->updated_by = auth::user()->username;
        } elseif ($publicId) {
            $department = Department::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $department = Department::createNew();
            $department->created_by = auth::user()->username;
        }
        $department->fill($data);
        $department->name = isset($data['name']) ? trim($data['name']) : null;
        $department->notes = isset($data['notes']) ? trim($data['notes']) : null;

        $department->save();


        return $department;
    }

    public function findPhonetically($departmentName)
    {
        $departmentNameMeta = metaphone($departmentName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $departmentId = 0;
        $departments = Department::scope()->get();
        if (!empty($departments)) {
            foreach ($departments as $department) {
                if (!$department->name) {
                    continue;
                }
                $map[$department->id] = $department;
                $similar = similar_text($departmentNameMeta, metaphone($department->name), $percent);
                if ($percent > $max) {
                    $departmentId = $department->id;
                    $max = $percent;
                }
            }
        }

        return ($departmentId && isset($map[$departmentId])) ? $map[$departmentId] : null;
    }
}
