<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Product extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ProductPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $table = 'Products';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'barcode',
        'tag',
        'notes',
        'cost',
        'item_category_id',
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

    /**
     * @return array
     */
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

    /**
     * @return array
     */
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

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PRODUCT;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    /**
     * @return mixed
     */
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

    public function itemCategory()
    {
        return $this->belongsTo('App\Models\ItemCategory', 'item_category_id')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id')->withTrashed();
    }

    public function itemMovements()
    {
        return $this->morphMany('\App\Models\ItemMovement', 'movable', 'movable_type', 'movable_id');
    }

}
