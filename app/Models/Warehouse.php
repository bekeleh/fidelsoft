<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Warehouse.
 */
class Warehouse extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\WarehousePresenter';

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'location_id',
        'notes',
        'is_deleted',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_WAREHOUSE;
    }

    public function getRoute()
    {
        return "/warehouses/{$this->public_id}/edit";
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

    public function branches()
    {
        return $this->hasMany('App\Models\Branch', 'store_id')->withTrashed();
    }

    public function item_stores()
    {
        return $this->hasMany('App\Models\ItemStore', 'store_id')->withTrashed();
    }

    public function products()
    {
        return $this->belongsToMany('\App\Models\Product', 'item_stores', 'store_id', 'product_id')->withPivot('id', 'qty', 'created_at', 'user_id')->withTrashed();
    }

    public function scopeHasQuantity($query)
    {
        $query = $query->whereHas('item_stores', function ($query) {
            $query->where('item_stores.qty', '>', 0)
                ->Where('item_stores.is_locked', false)
                ->Where('item_stores.is_deleted', false);
        });

        return $query;
    }

}
