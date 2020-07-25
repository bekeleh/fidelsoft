<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ItemBrand.
 */
class ItemBrand extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemBrandPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $table = 'item_brands';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'item_category_id',
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [];
    protected $casts = [];
    protected $appends = [];


    public function getEntityType()
    {
        return ENTITY_ITEM_BRAND;
    }

    public function getRoute()
    {
        return "/item_brands/{$this->public_id}/edit";
    }

    public static function findItemBrandByKey($key)
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

    public function item_category()
    {
        return $this->belongsTo('App\Models\itemCategory')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->withTrashed();
    }

    public function scopeBrandWithCategory($query)
    {
        $query = $query
            ->leftJoin('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->whereNull('item_brands.deleted_at')
            ->select(
                'item_brands.id',
                'item_brands.public_id',
                'item_brands.name',
                DB::raw("COALESCE(CONCAT(NULLIF(item_brands.name,''), ' ', NULLIF(item_categories.name,'')), NULLIF(item_brands.name,'')) name")
            );

        return $query->whereNotNull('item_brands.name');
    }

}
