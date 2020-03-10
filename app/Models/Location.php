<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Location.
 */
class Location extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\LocationPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $table = 'locations';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'location_code',
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
            'location_code',
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
            'location_code|code' => 'location_code',
        ];
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_LOCATION;
    }

    public function getUpperAttributes()
    {
        return strtoupper($this->name);
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

    public function stores()
    {
        return $this->hasMany('App\Models\Store')->withTrashed();
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/locations/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
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
