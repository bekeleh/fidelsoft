<?php

namespace App\Ninja\Repositories;

use App\Models\PermissionGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionGroupRepository extends BaseRepository
{
    private $model;

    public function __construct(PermissionGroup $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\PermissionGroup';
    }

    public function all()
    {
        return PermissionGroup::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('permission_groups')
            ->join('accounts', 'accounts.id', '=', 'permission_groups.account_id')
            ->join('users_groups', 'users_groups.group_id', '=', 'permission_groups.id')
//            ->join('users', 'users.id', '=', 'users_groups.user_id')
            ->where('permission_groups.account_id', '=', $accountId)
            //->where('permission_groups.deleted_at', '=', null)
            ->select(
                'permission_groups.id',
                'permission_groups.public_id',
                'permission_groups.name as permission_group_name',
                'permission_groups.is_deleted',
                'permission_groups.notes',
                'permission_groups.created_at',
                'permission_groups.updated_at',
                'permission_groups.deleted_at',
                'permission_groups.created_by',
                'permission_groups.updated_by',
                'permission_groups.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('permission_groups.name', 'like', '%' . $filter . '%')
                    ->orWhere('permission_groups.notes', 'like', '%' . $filter . '%');
            });
        }

//    if the entity type can be direct capitalized or if not a problem, skip the third parameters.
        $this->applyFilters($query, ENTITY_PERMISSION_GROUP);

        return $query;
    }

    public function save($data, $permissionGroup = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($permissionGroup) {
            $permissionGroup->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $permissionGroup = PermissionGroup::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in permission group repo save');
        } else {
            $permissionGroup = PermissionGroup::createNew();
            $permissionGroup->created_by = Auth::user()->username;
        }
        $permissionGroup->fill($data);
        $permissionGroup->name = isset($data['name']) ? ucwords(Str::lower(trim($data['name']))) : '';
        $permissionGroup->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $permissionGroup->save();

        return $permissionGroup;
    }

    public function findPhonetically($permissionGroupName)
    {
        $permissionGroupNameMeta = metaphone($permissionGroupName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $permissionGroupId = 0;
        $permissionGroups = PermissionGroup::scope()->get();
        if (!empty($permissionGroups)) {
            foreach ($permissionGroups as $permissionGroup) {
                if (!$permissionGroup->name) {
                    continue;
                }
                $map[$permissionGroup->id] = $permissionGroup;
                $similar = similar_text($permissionGroupNameMeta, metaphone($permissionGroup->name), $percent);
                if ($percent > $max) {
                    $permissionGroupId = $permissionGroup->id;
                    $max = $percent;
                }
            }
        }
        return ($permissionGroupId && isset($map[$permissionGroupId])) ? $map[$permissionGroupId] : null;
    }

}