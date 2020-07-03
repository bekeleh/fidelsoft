<?php

namespace App\Ninja\Repositories;

use App\Models\ScheduleCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleCategoryRepository extends BaseRepository
{
    private $model;

    public function __construct(ScheduleCategory $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ScheduleCategory';
    }

    public function all()
    {
        return ScheduleCategory::scope()->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('schedule_categories')
        ->leftJoin('accounts', 'accounts.id', '=', 'schedule_categories.account_id')
        ->leftJoin('users', 'users.id', '=', 'schedule_categories.user_id')
        ->where('schedule_categories.account_id', '=', $accountId)
//            ->where('schedule_categories.deleted_at', '=', null)
        ->select(
            'schedule_categories.name as schedule_category_name',
            'schedule_categories.public_id',
            'schedule_categories.user_id',
            'schedule_categories.notes',
            'schedule_categories.text_color',
            'schedule_categories.bg_color',
            'schedule_categories.created_by',
            'schedule_categories.updated_by',
            'schedule_categories.deleted_by',
            'schedule_categories.created_at',
            'schedule_categories.updated_at',
            'schedule_categories.deleted_at',
            'schedule_categories.is_deleted'
        );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('schedule_categories.name', 'like', '%' . $filter . '%')
                ->orwhere('schedule_categories.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_SCHEDULE_CATEGORY);

        return $query;
    }

    public function save($data, $scheduleCategory = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($scheduleCategory) {
            $scheduleCategory->updated_by = Auth::user()->username;

        } elseif ($publicId) {
            $scheduleCategory = ScheduleCategory::scope($publicId)->withArchived()->firstOrFail();
            Log::warning('Entity not set in schedule category repo save');
        } else {
            $scheduleCategory = ScheduleCategory::createNew();
            $scheduleCategory->created_by = Auth::user()->name;
        }

        $scheduleCategory->fill($data);

        $scheduleCategory->save();

        return $scheduleCategory;
    }
}
