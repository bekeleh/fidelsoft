<?php

namespace App\Ninja\Repositories;

use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('statuses')
            ->join('accounts', 'accounts.id', '=', 'statuses.account_id')
            ->join('users', 'users.id', '=', 'statuses.user_id')
            ->where('statuses.account_id', '=', $accountId)
//            ->where('statuses.deleted_at', '=', null)
            ->select(
                'statuses.id',
                'statuses.public_id',
                'statuses.name as status_name',
                'statuses.is_deleted',
                'statuses.notes',
                'statuses.created_at',
                'statuses.updated_at',
                'statuses.deleted_at',
                'statuses.created_by',
                'statuses.updated_by',
                'statuses.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('statuses.name', 'like', '%' . $filter . '%')
                    ->orWhere('statuses.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_STATUS);

        return $query;
    }

    public function save($data, $Status = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($Status) {
            $Status->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $Status = Status::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $Status = Status::createNew();
            $Status->created_by = Auth::user()->username;
        }
        $Status->fill($data);
        $Status->name = isset($data['name']) ? trim($data['name']) : '';
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