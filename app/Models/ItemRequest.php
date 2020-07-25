<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use App\Events\ItemRequestWasCreated;
use App\Events\ItemRequestWasUpdated;

/**
 * Model Class ItemRequestPresenter.
 */
class ItemRequest extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemRequestPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['approved_date', 'created_at', 'deleted_at', 'deleted_at'];

    protected $casts = [];
    protected $hidden = [];
    protected $appends = [];

    protected $fillable = [
        'product_id',
        'department_id',
        'warehouse_id',
        'status_id',
        'qty',
        'delivered_qty',
        'qty',
        'required_date',
        'dispatch_date',
        'is_deleted',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function getEntityType()
    {
        return ENTITY_ITEM_REQUEST;
    }

    public function getRoute()
    {
        return "/item_requests/{$this->public_id}/edit";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function department()
    {
        return $this->belongsTo('\App\Models\Department');
    }

    public function product()
    {
        return $this->belongsTo('\App\Models\Product');
    }

    public function status()
    {
        return $this->belongsTo('\App\Models\Status');
    }

    public function warehouse()
    {
        return $this->belongsTo('\App\Models\WareHouse');
    }

    public static function calcStatusLabel($qoh, $reorderLevel)
    {
        if (!$qoh || !$reorderLevel) {
            return false;
        }
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
        if (!$qoh || !$reorderLevel) {
            return false;
        }

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

ItemRequest::created(function ($itemRequest) {
    event(new ItemRequestWasCreated($itemRequest));
});

ItemRequest::updating(function ($itemRequest) {
    event(new ItemRequestWasUpdated($itemRequest));
});
