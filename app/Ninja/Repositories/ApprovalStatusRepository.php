<?php

namespace App\Ninja\Repositories;

use App\Models\ApprovalStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovalStatusRepository extends BaseRepository
{
    private $model;

    public function __construct(ApprovalStatus $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ApprovalStatus';
    }

    public function all()
    {
        return ApprovalStatus::scope()->withTrashed()->where('is_deleted', '=', false)->get();
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
                'approval_statuses.name as approval_status_name',
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

    public function save($data, $approvalStatus = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($approvalStatus) {
            $approvalStatus->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $approvalStatus = ApprovalStatus::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in approval status repo save');
        } else {
            $approvalStatus = ApprovalStatus::createNew();
            $approvalStatus->created_by = Auth::user()->username;
        }
        $approvalStatus->fill($data);
        $approvalStatus->name = isset($data['name']) ? ucwords(Str::lower(trim($data['name']))) : '';
        $approvalStatus->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $approvalStatus->save();

        return $approvalStatus;
    }

    public function findPhonetically($approvalStatusName)
    {
        $approvalStatusNameMeta = metaphone($approvalStatusName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $approvalStatusId = 0;
        $approvalStatuses = ApprovalStatus::scope()->get();
        if (!empty($approvalStatuses)) {
            foreach ($approvalStatuses as $approvalStatus) {
                if (!$approvalStatus->name) {
                    continue;
                }
                $map[$approvalStatus->id] = $approvalStatus;
                $similar = similar_text($approvalStatusNameMeta, metaphone($approvalStatus->name), $percent);
                if ($percent > $max) {
                    $approvalStatusId = $approvalStatus->id;
                    $max = $percent;
                }
            }
        }

        return ($approvalStatusId && isset($map[$approvalStatusId])) ? $map[$approvalStatusId] : null;
    }
}