<?php

namespace App\Ninja\Repositories;

use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatusRepository extends BaseRepository
{
    private $model;

    public function __construct(Status $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Status';
    }

    public function all()
    {
        return Status::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('approval_statuses')
            ->join('accounts', 'accounts.id', '=', 'approval_statuses.account_id')
            ->join('users', 'users.id', '=', 'approval_statuses.user_id')
            ->where('approval_statuses.account_id', '=', $accountId)
//            ->where('approval_statuses.deleted_at', '=', null)
            ->select(
                'approval_statuses.id',
                'approval_statuses.public_id',
                'approval_statuses.name as status_name',
                'approval_statuses.is_deleted',
                'approval_statuses.notes',
                'approval_statuses.created_at',
                'approval_statuses.updated_at',
                'approval_statuses.deleted_at',
                'approval_statuses.created_by',
                'approval_statuses.updated_by',
                'approval_statuses.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('approval_statuses.name', 'like', '%' . $filter . '%')
                    ->orWhere('approval_statuses.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_APPROVAL_STATUS);

        return $query;
    }

    public function save($data, $Status = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($Status) {
            $Status->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $Status = Status::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in approval status repo save');
        } else {
            $Status = Status::createNew();
            $Status->created_by = Auth::user()->username;
        }
        $Status->fill($data);
        $Status->name = isset($data['name']) ? ucwords(Str::lower(trim($data['name']))) : '';
        $Status->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $Status->save();

        return $Status;
    }

    public function findPhonetically($StatusName)
    {
        $StatusNameMeta = metaphone($StatusName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $StatusId = 0;
        $Statuses = Status::scope()->get();
        if (!empty($Statuses)) {
            foreach ($Statuses as $Status) {
                if (!$Status->name) {
                    continue;
                }
                $map[$Status->id] = $Status;
                $similar = similar_text($StatusNameMeta, metaphone($Status->name), $percent);
                if ($percent > $max) {
                    $StatusId = $Status->id;
                    $max = $percent;
                }
            }
        }

        return ($StatusId && isset($map[$StatusId])) ? $map[$StatusId] : null;
    }
}