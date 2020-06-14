<?php

namespace App\Ninja\Repositories;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;

class PermissionRepository extends BaseRepository
{
    private $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Permission';
    }

    public function all()
    {
        return Permission::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('permissions')
            ->join('accounts', 'accounts.id', '=', 'permissions.account_id')
            ->join('users', 'users.id', '=', 'permissions.user_id')
            ->where('permissions.account_id', '=', $accountId)
            //->where('permissions.deleted_at', '=', null)
            ->select(
                'permissions.id',
                'permissions.public_id',
                'permissions.name as group_name',
                'permissions.is_deleted',
                'permissions.notes',
                'permissions.created_at',
                'permissions.updated_at',
                'permissions.deleted_at',
                'permissions.created_by',
                'permissions.updated_by',
                'permissions.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('permissions.name', 'like', '%' . $filter . '%')
                    ->orWhere('permissions.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_PERMISSION_GROUP, 'permission_group');

        return $query;
    }

    public function save($data, $permission = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($permission) {
            $permission->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $permission = Permission::scope($publicId)->withArchived()->firstOrFail();
            Log::warning('Entity not set in permission repo save');
        } else {
            $permission = Permission::createNew();
            $permission->created_by = Auth::user()->username;
        }
        $permission->fill($data);
        $permission->name = isset($data['name']) ? ucwords(Str::lower(trim($data['name']))) : '';
        $permission->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $permission->save();
        return $permission;
    }

    public function findPhonetically($permissionName)
    {
        $permissionNameMeta = metaphone($permissionName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $permissionId = 0;
        $permissions = Permission::scope()->get();
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                if (!$permission->name) {
                    continue;
                }
                $map[$permission->id] = $permission;
                $similar = similar_text($permissionNameMeta, metaphone($permission->name), $percent);
                if ($percent > $max) {
                    $permissionId = $permission->id;
                    $max = $percent;
                }
            }
        }

        return ($permissionId && isset($map[$permissionId])) ? $map[$permissionId] : null;
    }
}