<?php

namespace App\Models;

use App\Models\EntityModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Scheduled Schedule
 */
class Schedule extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\SchedulePresenter';
    protected $table = 'schedules';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
        'notes',
        'rrule',
        'url',
        'will_call',
        'isRecurring',
        'is_deleted',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_SCHEDULE;
    }

    public function getRoute()
    {
        return "/schedules/{$this->public_id}/edit";
    }

    //necessary here for scope below
    public function getStartDateAttribute()
    {
        return Carbon::parse($this->attributes['start_date'])->format('Y-m-d H:i');
    }

    public function getEndDateAttribute()
    {
        return Carbon::parse($this->attributes['end_date'])->format('Y-m-d H:i');
    }

    public function scopeWithOccurrences($query)
    {
        $query->leftjoin('schedule_occurrences', 'schedule.id', '=', 'schedule_occurrences.schedule_id');
    }

    public function category()
    {
        return $this->hasOne('App\Models\ScheduleCategory');
    }

    public function occurrences()
    {
        return $this->hasMany('App\Models\ScheduleOccurrence');
    }

    public function reminders()
    {
        return $this->hasMany('App\Models\ScheduleReminder');
    }

    public function resources()
    {
        return $this->hasMany('App\Models\ScheduleResource');
    }

}
