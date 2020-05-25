<?php

namespace App\Ninja\Repositories;

use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleRepository extends BaseRepository
{
    private $model;

    public function __construct(Schedule $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Schedule';
    }

    public function all()
    {
        return Schedule::scope()->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('schedules')
            ->where('schedules.account_id', '=', $accountId)
//            ->where('schedules.deleted_at', '=', null)
            ->select(
                'schedules.public_id',
                'schedules.user_id',
                'schedules.title',
                'schedules.description',
                'schedules.notes',
                'schedules.rrule',
                'schedules.url',
                'schedules.will_call',
                'schedules.isRecurring',
                'schedules.is_deleted',
                'schedules.created_by',
                'schedules.updated_by',
                'schedules.deleted_by',
                'schedules.created_at',
                'schedules.updated_at',
                'schedules.deleted_at',
                'schedules.is_deleted'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('schedules.title', 'like', '%' . $filter . '%')
                    ->orwhere('schedules.description', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_SCHEDULE);

        return $query;
    }

    public function save($data, $Schedule = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($Schedule) {
            $Schedule->updated_by = Auth::user()->username;

        } elseif ($publicId) {
            $Schedule = Schedule::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in schedule report repo save');
        } else {
            $Schedule = Schedule::createNew();
            $Schedule->created_by = Auth::user()->name;
        }

        $Schedule->fill($data);

        $Schedule->save();

        return $Schedule;
    }
}
