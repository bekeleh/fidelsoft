<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Department.
 */
class Department extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\DepartmentPresenter';
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
        return ENTITY_DEPARTMENT;
    }

    public function getRoute()
    {
        return "/departments/{$this->public_id}";
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

    public static function findDepartmentByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
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

    public function itemRequest()
    {
        return $this->hasMany('App\Models\ItemRequest')->withTrashed();
    }

}
