<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Dashboard.
 *
 * @property-read Account $account
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard newQuery()
 * @method static Builder|Dashboard onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Dashboard withTrashed()
 * @method static Builder|Dashboard withoutTrashed()
 * @mixin Eloquent
 */
class Dashboard extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\DashboardPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'company_id',
        'is_deleted',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_DASHBOARD;
    }

    public function getRoute()
    {
        return "/dashboard/{$this->public_id}";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

}
