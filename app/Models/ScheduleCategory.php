<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ScheduleCategory.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property int|null $is_deleted
 * @property string|null $name
 * @property string|null $text_color
 * @property string|null $bg_color
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory newQuery()
 * @method static Builder|ScheduleCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereTextColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleCategory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ScheduleCategory withTrashed()
 * @method static Builder|ScheduleCategory withoutTrashed()
 * @mixin Eloquent
 */
class ScheduleCategory extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\ScheduleCategoryPresenter';

    protected $table = 'schedule_categories';
    protected $dates = ['created_at', 'updated_at'];
    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'name',
        'text_color',
        'bg_color',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_SCHEDULE_CATEGORY;
    }

    public function getRoute()
    {
        return "/schedule_categories/{$this->public_id}/edit";
    }

}
