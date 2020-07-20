<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ItemStore.
 */
class ItemStore extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\ItemStorePresenter';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $appends = [];
    protected $hidden = [];
    protected $casts = [];

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'bin',
        'qty',
        'reorder_level',
        'EOQ',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_ITEM_STORE;
    }

    public function getRoute()
    {
        return "/item_stores/{$this->public_id}/edit";
    }

    public static function findProductByKey($key)
    {
        return self::scope()->where('bin', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product')->withTrashed();
    }

    public function stockMovements()
    {
        return $this->morphMany('\App\Models\ItemMovement', 'movable', 'movable_type', 'movable_id');
    }

    public static function calcStatusLabel($qoh, $reorderLevel)
    {
        if ($qoh) {
            if (floatval($qoh) > 0) {
                $label = $qoh;
            } else {
                $label = $qoh;
            }
        }
        return $label;
    }

    public static function calcStatusClass($qoh, $reorderLevel)
    {
        if (!empty($qoh) && !empty($reorderLevel)) {
            if (floatval($qoh) > floatval($reorderLevel)) {
//                return 'default';
                return 'success';
            } else {
                return 'danger';
            }
        } elseif (!empty($qoh)) {
            return 'primary';
        } else {
            return 'warning';
        }
    }
}
