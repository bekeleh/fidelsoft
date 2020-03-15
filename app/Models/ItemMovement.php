<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ItemMovement.
 */
class ItemMovement extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemMovementPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $table = 'item_movements';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'movable_id',
        'movable_type',
        'qty',
        'qoh',
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
        return ENTITY_ITEM_MOVEMENT;
    }

    public function getUpperAttributes()
    {
        return strtoupper($this->name);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function movable()
    {
        return $this->morphTo();
    }

    public function itemStore()
    {
        return $this->belongsTo('\App\Models\ItemStore', 'movable_id')
            ->where('movable_type', '=', ItemStore::class);
    }

    public function item()
    {
        return $this->belongsTo('\App\Models\Product', $this->movable());
    }

    public function store()
    {
        return $this->belongsTo('\App\Models\Store', $this->movable());
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
