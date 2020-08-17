<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $account_id
 * @property int|null $client_id
 * @property int|null $public_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $name
 * @property float $task_rate
 * @property string|null $due_date
 * @property int $is_deleted
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property string|null $private_notes
 * @property float $budgeted_hours
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read Collection|Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|Project dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static Builder|Project onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereBudgetedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereTaskRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Project withTrashed()
 * @method static Builder|Project withoutTrashed()
 * @mixin Eloquent
 */
class Project extends EntityModel
{
    // Expense Categories
    use SoftDeletes;
    use PresentableTrait;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'task_rate',
        'private_notes',
        'due_date',
        'budgeted_hours',
        'custom_value1',
        'custom_value2',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $presenter = 'App\Ninja\Presenters\ProjectPresenter';

    public function getEntityType()
    {
        return ENTITY_PROJECT;
    }

    public function getRoute()
    {
        return "/projects/{$this->public_id}";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        });
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}

Project::creating(function ($project) {
    $project->setNullValues();
});

Project::updating(function ($project) {
    $project->setNullValues();
});
