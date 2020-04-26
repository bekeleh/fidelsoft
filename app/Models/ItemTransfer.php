<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use App\Events\ItemTransferWasCreated;
use App\Events\ItemTransferWasUpdated;

/**
 * Model Class ItemTransferPresenter.
 */
class ItemTransfer extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemTransferPresenter';
    use PresentableTrait;
    use SoftDeletes;


    protected $table = 'item_transfers';
    protected $dates = ['approved_date', 'created_at', 'deleted_at', 'deleted_at'];

    protected $casts = [];
    protected $hidden = [];
    protected $appends = [];


    protected $fillable = [
        'product_id',
        'previous_store_id',
        'current_store_id',
        'status_id',
        'approver_id',
        'approved_date',
        'qty',
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
        return "/item_transfers/{$this->public_id}/edit";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function itemMovements()
    {
        return $this->morphMany('\App\Models\ItemMovement', 'movable', 'movable_type', 'movable_id');
    }

    public function item()
    {
        return $this->belongsTo('\App\Models\Product', 'product_id');
    }

    public function status()
    {
        return $this->belongsTo('\App\Models\Status', 'status_id');
    }

    public function approver()
    {
        return $this->belongsTo('\App\Models\User', 'approver_id');
    }

    public function previousStore()
    {
        return $this->belongsTo('\App\Models\Store', 'previous_store_id');
    }

    public function currentStore()
    {
        return $this->belongsTo('\App\Models\Store', 'current_store_id');
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
    event(new ItemTransferWasCreated($itemTransfer));
});

ItemTransfer::updating(function ($itemTransfer) {
    event(new ItemTransferWasUpdated());
});
