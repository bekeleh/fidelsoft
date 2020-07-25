<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Dashboard.
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
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

}
