<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Store.
 */
class ItemCategory extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemCategoryPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $appends = [];
    protected $table = 'item_categories';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $hidden = [];
    protected $casts = [];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_ITEM_CATEGORY;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function findItemCategoryByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'item_category_id')->withTrashed();
    }

    /**
     * -----------------------------------------------
     * BEGIN QUERY SCOPES
     * -----------------------------------------------
     * @param $query
     * @param $date_from
     * @param $date_to
     * @return mixed
     */

    public function scopeDateBetween($query, $date_from, $date_to)
    {
        return $query->whereBetween('created_at', [$date_from, $date_to]);
    }

}