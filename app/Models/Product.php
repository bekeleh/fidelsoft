<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        'product_key',
        'public_id',
        'upc',
        'item_serial',
        'item_barcode',
        'item_tag',
        'notes',
        'cost',
        'is_locked',
        'is_public',
        'item_brand_id',
        'tax_category_id',
        'item_type_id',
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
            'product_key',
            'upc',
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
        return self::scope()->where('product_key', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ItemType')->withTrashed();
    }

    public function tax_categories()
    {
        return $this->belongsTo('App\Models\TaxCategory')->withTrashed();
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

    public function item_brand()
    {
        return $this->belongsTo('App\Models\ItemBrand')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit')->withTrashed();
    }

    public function item_movements()
    {
        return $this->morphMany('\App\Models\ItemMovement', 'movable', 'movable_type', 'movable_id');
    }

    public function manufacturer_product_details()
    {
        return $this->hasMany('App\Models\ItemPrice')->withTrashed();
    }

    public function scopeStock($query, $publicId = false, $accountId = false)
    {
        $storeId = isset(auth::user()->branch) ? auth::user()->branch->store_id : null;
        if (!isset($storeId)) {
            return false;
        }
        $query = $query->whereHas('item_stores', function ($query) use ($storeId) {
            $query->where('item_stores.store_id', $storeId)
                ->where('item_stores.qty', '>', 0)
                ->Where('item_stores.is_locked', false)
                ->Where('item_stores.is_deleted', false);
        });

        return $query;
    }

    public function scopeProductWithBrand($query)
    {
        $query = DB::table('products')
            ->leftJoin('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
//            ->where('products.account_id', '=', $accountId)
            //->where('products.deleted_at', '=', null)
            ->select(
                'products.id',
                'products.public_id',
                DB::raw("CONCAT(NULLIF(products.product_key,''), ' ', NULLIF(item_brands.name,'')) product_key")
            );

        return $query;
    }

    public function scopeProducts($query)
    {
        $query = $query->whereHas('item_brand', function ($query) {
            $query->Where('item_brands.is_deleted', false)
                ->whereHas('item_category', function ($query) {
                    $query->Where('item_categories.is_deleted', false);
                });
        })->with(['item_brand.item_category'])->orderby('product_key')->get();

        $query = $this->getProductDisplayName($query);

        return $query;
    }

    public function getProductDisplayName($query)
    {
        if (is_null($query)) {
            return false;
        }

        foreach ($query as $subQuery) {
            $name_str = '';
            if (isset($subQuery->item_brand->item_category)) {
                $name_str .= e($subQuery->product_key) . ' ';
                if (isset($subQuery->item_brand->item_category->name)) {
                    $name_str .= ' (' . e($subQuery->item_brand->name) . ')' . ' (' . e($subQuery->item_brand->item_category->name) . ')' . ' ';
                } else {
                    $name_str .= ' (' . e($subQuery->item_brand->name) . ')' . ' ';
                }
            } else {
                if (isset($subQuery->item_brand)) {
                    $name_str .= ' (' . e($subQuery->item_brand->name) . ')' . ' (' . e($subQuery->item_brand->name) . ')' . ' ';
                } else {
                    $name_str .= ' (' . e($subQuery->item_brand->name) . ')' . ' ';
                }
            }

            $subQuery->product_key = $name_str;
        }

        return $query;
    }

}
