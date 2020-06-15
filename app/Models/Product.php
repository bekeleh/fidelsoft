<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laracasts\Presenter\PresentableTrait;

class Product extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\ProductPresenter';

    protected $appends = [];
    protected $casts = [];
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];
    protected $hidden = [];

    protected $fillable = [
        'name',
        'UPC',
        'EAN',
        'item_serial',
        'item_barcode',
        'item_tag',
        'notes',
        'cost',
        'is_locked',
        'is_public_id',
        'item_brand_id',
        'unit_id',
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'custom_value1',
        'custom_value2',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public static function getImportColumns()
    {
        return [
            'name',
            'UPC',
            'EAN',
            'notes',
            'cost',
            'custom_value1',
            'custom_value2',
        ];
    }

    public static function getImportMap()
    {
        return [
            'product|item' => 'name',
            'notes|description|details' => 'notes',
            'cost|amount|price' => 'cost',
            'custom_value1' => 'custom_value1',
            'custom_value2' => 'custom_value2',
        ];
    }


    public function getEntityType()
    {
        return ENTITY_PRODUCT;
    }

    public static function findProductByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function stores()
    {
        return $this->belongsToMany('App\Models\Store', 'item_stores', 'product_id', 'store_id')->withPivot('id', 'qty', 'created_at', 'user_id')->withTrashed();
    }

    public function item_stores()
    {
        return $this->hasMany('App\Models\ItemStore')->withTrashed();
    }

    public function item_prices()
    {
        return $this->hasMany('App\Models\ItemPrice')->withTrashed();
    }

    public function itemBrand()
    {
        return $this->belongsTo('App\Models\ItemBrand')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit')->withTrashed();
    }

    public function itemMovements()
    {
        return $this->morphMany('\App\Models\ItemMovement', 'movable', 'movable_type', 'movable_id');
    }

    public function manufacturerProductDetails()
    {
        return $this->hasMany('App\Models\ItemPrice')->withTrashed();
    }

    public function scopeStock($query, $publicId = false, $accountId = false)
    {
        if (!auth::check() || !auth::user()->account_id || !auth::user()->branch->store_id) {
            return false;
        }

        $query = $query->whereHas('item_stores', function ($query) {
            $query->where('item_stores.store_id', '=', auth::user()->branch->store_id)
                ->where('item_stores.qty', '>', 0)
                ->Where('item_stores.is_deleted', '=', false);
        });

        return $query;
    }


}
