<?php

namespace App\Ninja\Repositories;


use App\Models\HoldReason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HoldReasonRepository extends BaseRepository
{
    private $model;

    public function __construct(HoldReason $model)
    {
        $this->model = $model;
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function getClassName()
    {
        return 'App\Models\HoldReason';
    }

    public function all()
    {
        return HoldReason::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }


    public function find($accountId, $filter = null)
    {
        $query = DB::table('hold_reasons')
            ->join('accounts', 'accounts.id', '=', 'hold_reasons.account_id')
            ->join('users', 'users.id', '=', 'hold_reasons.user_id')
            ->where('hold_reasons.account_id', '=', $accountId)
            //->where('hold_reasons.deleted_at', '=', null)
            ->select(
                'hold_reasons.id',
                'hold_reasons.public_id',
                'hold_reasons.name as hold_reason',
                'hold_reasons.allow_invoice',
                'hold_reasons.is_deleted',
                'hold_reasons.notes',
                'hold_reasons.created_at',
                'hold_reasons.updated_at',
                'hold_reasons.deleted_at',
                'hold_reasons.created_by',
                'hold_reasons.updated_by',
                'hold_reasons.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('hold_reasons.name', 'like', '%' . $filter . '%')
                    ->orWhere('hold_reasons.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_HOLD_REASON);

        return $query;
    }

    public function save($data, $holdReason = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($holdReason) {
            $holdReason->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $holdReason = HoldReason::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in hold reason repo save');
        } else {
            $holdReason = HoldReason::createNew();
            $holdReason->created_by = Auth::user()->username;
        }
        $holdReason->fill($data);
        $holdReason->name = isset($data['name']) ? (ucwords(trim($data['name']))) : '';

        $holdReason->save();

        return $holdReason;
    }

    public function findPhonetically($holdReason)
    {
        $holdReasonMeta = metaphone($holdReason);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $holdReasonId = 0;
        $holdReasons = HoldReason::scope()->get();
        if (!empty($holdReasons)) {
            foreach ($holdReasons as $holdReason) {
                if (!$holdReason->reason) {
                    continue;
                }
                $map[$holdReason->id] = $holdReason;
                $similar = similar_text($holdReasonMeta, metaphone($holdReason->reason), $percent);
                if ($percent > $max) {
                    $holdReasonId = $holdReason->id;
                    $max = $percent;
                }
            }
        }

        return ($holdReasonId && isset($map[$holdReasonId])) ? $map[$holdReasonId] : null;
    }
}