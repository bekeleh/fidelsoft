<?php

namespace App\Ninja\Repositories;


use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupRepository extends BaseRepository
{
    private $model;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Group';
    }

    public function all()
    {
        return Group::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('permission_groups')
            ->join('accounts', 'accounts.id', '=', 'permission_groups.account_id')
//            ->join('users', 'users.id', '=', 'users_groups.group_id')
            ->where('permission_groups.account_id', '=', $accountId)
            //->where('permission_groups.deleted_at', '=', null)
            ->select(
                'permission_groups.id',
                'permission_groups.public_id',
                'permission_groups.name as group_name',
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

        $this->applyFilters($query, ENTITY_GROUP, 'permission_group');

        return $query;
    }

    public function save($data, $group = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($group) {
            $group->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $group = Group::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in unit repo save');
        } else {
            $group = Group::createNew();
            $group->created_by = Auth::user()->username;
        }
        $group->fill($data);
        $group->name = isset($data['name']) ? ucwords(Str::lower(trim($data['name']))) : '';
        $group->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $group->save();
        return $group;
    }

    public function findPhonetically($groupName)
    {
        $groupNameMeta = metaphone($groupName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $groupId = 0;
        $groups = Group::scope()->get();
        if (!empty($groups)) {
            foreach ($groups as $group) {
                if (!$group->name) {
                    continue;
                }
                $map[$group->id] = $group;
                $similar = similar_text($groupNameMeta, metaphone($group->name), $percent);
                if ($percent > $max) {
                    $groupId = $group->id;
                    $max = $percent;
                }
            }
        }
        return ($groupId && isset($map[$groupId])) ? $map[$groupId] : null;
    }

    public function decodePermissions()
    {
        return $this->model->decodePermissions();
    }
}