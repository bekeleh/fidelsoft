<?php

namespace App\Models;

use App\Models\EntityModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Location.
 */
class Location extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\LocationPresenter';

    protected $table = 'locations';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $hidden = [];
    protected $casts = [];


    public static function getImportColumns()
    {
        return [
            'name',
            'location_code',
            'notes',
        ];
    }

    public static function getImportMap()
    {
        return [
            'name|Name' => 'name',
        ];
    }

    public function getEntityType()
    {
        return ENTITY_LOCATION;
    }

    public function getRoute()
    {
        return "/locations/{$this->public_id}/edit";
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    public function getUpperAttributes()
    {
        return strtoupper($this->name);
    }

    public static function findLocationByName($key)
    {
        return self::scope()->where('name', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function users()
    {
        return $this->hasMany('App\Models\User')->withTrashed();
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function stores()
    {
        return $this->hasMany('App\Models\Store')->withTrashed();
    }

    public function branches()
    {
        return $this->hasMany('App\Models\Branch')->withTrashed();
    }

}
