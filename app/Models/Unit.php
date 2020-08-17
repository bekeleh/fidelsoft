<?php

namespace App\Models;

use App\Models\Common\EntityModel;
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
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->withTrashed();
    }
    public static function selectOptions()
    {
        $units = Unit::where('account_id', null)->get();

        foreach (Unit::scope()->get() as $unit) {
            $units->push($unit);
        }

        foreach($units as $unit){
            $name = Str::snake(str_replace(' ', '_', $unit->name));
            $units->name = trans('texts.unit_' . $name);
        }

        return $units->sortBy('name');
    }

}
