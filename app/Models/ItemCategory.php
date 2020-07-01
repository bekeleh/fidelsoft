<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ItemCategory.
 */
class ItemCategory extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemCategoryPresenter';
    use PresentableTrait;
    use SoftDeletes;


    protected $table = 'item_categories';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];
    protected $appends = [];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_ITEM_CATEGORY;
    }

    public function getRoute()
    {
        return "/item_categories/{$this->public_id}/edit";
    }

    public static function findItemCategoryByKey($key)
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

    public function item_brands()
    {
        return $this->hasMany('App\Models\ItemBrand')->withTrashed();
    }
}
