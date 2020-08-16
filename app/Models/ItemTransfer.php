<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use App\Events\ItemTransferWasCreatedEvent;
use App\Events\ItemTransferWasUpdatedEvent;

/**
 * Model Class ItemTransferPresenter.
 */
class ItemTransfer extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\ItemTransferPresenter';

    protected $dates = ['approved_date', 'created_at', 'deleted_at'];

    protected $casts = [];
    protected $hidden = [];
    protected $appends = [];


    protected $fillable = [
        'product_id',
        'previous_warehouse_id',
        'current_warehouse_id',
        'status_id',
        'approver_id',
        'dispatch_date',
        'qty',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function getEntityType()
    {
        return ENTITY_ITEM_TRANSFER;
    }

    public function getRoute()
    {
        return "/item_transfers/{$this->public_id}/edit";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function stockMovements()
    {
        return $this->morphMany('\App\Models\ItemMovement', 'movable', 'movable_type', 'movable_id');
    }

    public function product()
    {
        return $this->belongsTo('\App\Models\Product');
    }

    public function status()
    {
        return $this->belongsTo('\App\Models\Status');
    }

    public function approver()
    {
        return $this->belongsTo('\App\Models\User');
    }

    public function previousWarehouse()
    {
        return $this->belongsTo('\App\Models\Warehouse', 'previous_warehouse_id');
    }

    public function currentWarehouse()
    {
        return $this->belongsTo('\App\Models\Warehouse', 'current_warehouse_id');
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

ItemTransfer::created(function ($itemTransfer) {
    event(new ItemTransferWasCreatedEvent($itemTransfer));
});

ItemTransfer::updating(function ($itemTransfer) {
    event(new ItemTransferWasUpdatedEvent());
});
