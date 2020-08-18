<?php

namespace App\Models;

use App\Models\EntityModel;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Scheduled Report
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $ip
 * @property string|null $frequency
 * @property string|null $config
 * @property string|null $send_date
 * @property int $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport newQuery()
 * @method static Builder|ScheduledReport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereSendDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledReport whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ScheduledReport withTrashed()
 * @method static Builder|ScheduledReport withoutTrashed()
 * @mixin Eloquent
 */
class ScheduledReport extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\ScheduledReportPresenter';
    protected $table = 'scheduled_reports';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'frequency',
        'config',
        'send_date',
        'created_by',
        'updated_by',
        'deleted_by',
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
        return $this->belongsTo('App\Models\Common\Account');
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
