<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
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
        return self::Scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function itemCategory()
    {
        return $this->belongsTo('App\Models\itemCategory', 'item_category_id')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'item_brand_id')->withTrashed();
    }

}
