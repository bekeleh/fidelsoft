<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Product extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ProductPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'item_serial',
        'item_barcode',
        'item_tag',
        'notes',
        'item_cost',
        'item_brand_id',
        'unit_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'custom_value1',
        'custom_value2',
    ];
    protected $hidden = [];
    protected $casts = [];


    public static function getImportColumns()
    {
        return [
            'name',
            'notes',
            'item_cost',
            'custom_value1',
            'custom_value2',
        ];
    }

    public static function getImportMap()
    {
        return [
            'product|item' => 'name',
            'notes|description|details' => 'notes',
            'item_cost|amount|price' => 'item_cost',
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
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function stores()
    {
        return $this->hasMany('App\Models\ItemStore', 'store_id')->withTrashed();
    }

    public function itemPrices()
    {
        return $this->hasMany('App\Models\ItemPrice', 'product_id')->withTrashed();
    }

    public function itemBrand()
    {
        return $this->belongsTo('App\Models\ItemBrand', 'item_brand_id')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id')->withTrashed();
    }

    public function itemMovements()
    {
        return $this->morphMany('\App\Models\ItemMovement', 'movable', 'movable_type', 'movable_id');
    }

    public function manufacturerProductDetails()
    {
        return $this->hasMany('App\Models\ItemPrice', 'product_id')->withTrashed();
    }

}
