<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Branch.
 */
class Branch extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\BranchPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'warehouse_id',
        'location_id',
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
        return ENTITY_BRANCH;
    }

    public function getRoute()
    {
        return "/branches/{$this->public_id}";
    }

    public function getUpperAttributes()
    {
        return strtoupper($this->name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    public static function findBranchByKey($key)
    {
        return self::scope()->where('name', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
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

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse')->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location')->withTrashed();
    }

//    public function warehouses()
//    {
//        return $this->hasMany('App\Models\Warehouse')->withTrashed();
//    }

    public function itemRequest()
    {
        return $this->hasMany('App\Models\ItemRequest')->withTrashed();
    }

}
