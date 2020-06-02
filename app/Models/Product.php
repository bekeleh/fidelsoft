<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    protected $hidden = [];
    protected $casts = [];


    public static function getImportColumns()
    {
        return [
            'name',
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

    public function scopeInventory($query, $publicId = false, $accountId = false)
    {
        $query = DB::table('products')
            ->leftJoin('item_brands', 'item_brands.id', 'products.item_brand_id')
            ->leftJoin('item_categories', 'item_categories.id', 'item_brands.item_category_id')
            ->leftJoin('item_stores', 'item_stores.product_id', 'products.id')
            ->leftJoin('item_stores', 'item_stores.store_id', 'stores.id')
            ->where('products.account_id', '=', $accountId)
//            ->where('products.is_deleted', '=', null)
            ->select
            (
                'products.id',
                'products.name'
            );


        return $query;
    }
}
