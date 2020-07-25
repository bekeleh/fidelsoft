<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Unit.
 */
class Unit extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\UnitPresenter';

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];
    protected $hidden = [];
    protected $casts = [];
    protected $appends = [];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function getEntityType()
    {
        return ENTITY_UNIT;
    }

    public function getRoute()
    {
        return "/units/{$this->public_id}/edit";
    }


    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->withTrashed();
    }
}
