<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Store.
 */
class Store extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\StorePresenter';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'store_code',
        'location_id',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_STORE;
    }

    public function getRoute()
    {
        return "/stores/{$this->public_id}/edit";
    }

    public function getUpperAttributes()
    {
        return strtoupper($this->name);
    }

    public static function findProductByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\User', 'manager_id')->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany('App\Models\ItemStore', 'product_id')->withTrashed();
    }

}
