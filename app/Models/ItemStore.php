<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Store.
 */
class ItemStore extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\StockPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $appends = [];
    protected $table = 'item_stores';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'product_id',
        'store_id',
        'bin',
        'qty',
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
        return ENTITY_ITEM_STORE;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function findProductByKey($key)
    {
        return self::scope()->where('bin', '=', $key)->first();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id')->withTrashed();
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
