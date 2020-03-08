<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

class Store extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\StorePresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $table = 'stores';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'store_code',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
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
            'store_code',
            'notes',
        ];
    }

    /**
     * @return array
     */
    public static function getImportMap()
    {
        return [
            'name|Name' => 'name',
            'store_code|code' => 'store_code',
        ];
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_STORE;
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

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
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
