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

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'item_category_id')->withTrashed();
    }

}
