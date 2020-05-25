<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Scheduled Report
 */
class ScheduledReport extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\ScheduledReportPresenter';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    protected $fillable = [
        'frequency',
        'config',
        'send_date',
    ];

    public function getEntityType()
    {
        return ENTITY_SCHEDULED_REPORT;
    }

    public function getRoute()
    {
        return "/scheduled_reports/{$this->public_id}/edit";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function updateSendDate()
    {
        switch ($this->frequency) {
            case REPORT_FREQUENCY_DAILY;
                $this->send_date = Carbon::now()->addDay()->toDateString();
                break;
            case REPORT_FREQUENCY_WEEKLY:
                $this->send_date = Carbon::now()->addWeek()->toDateString();
                break;
            case REPORT_FREQUENCY_BIWEEKLY:
                $this->send_date = Carbon::now()->addWeeks(2)->toDateString();
                break;
            case REPORT_FREQUENCY_MONTHLY:
                $this->send_date = Carbon::now()->addMonth()->toDateString();
                break;
        }

        $this->save();
    }
}
