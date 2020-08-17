<?php

namespace App\Models;


use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use App\Events\ItemTransferWasCreatedEvent;
use App\Events\ItemTransferWasUpdatedEvent;

/**
 * Model Class ItemTransferPresenter.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $previous_warehouse_id
 * @property int|null $current_warehouse_id
 * @property int|null $approver_id
 * @property int|null $status_id
 * @property int|null $qty
 * @property int $is_deleted
 * @property string|null $notes
 * @property string|null $dispatch_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $approver
 * @property-read Warehouse|null $currentWarehouse
 * @property-read Warehouse|null $previousWarehouse
 * @property-read Product|null $product
 * @property-read Status|null $status
 * @property-read Collection|ItemMovement[] $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer newQuery()
 * @method static Builder|ItemTransfer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereApproverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereCurrentWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereDispatchDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer wherePreviousWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemTransfer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ItemTransfer withTrashed()
 * @method static Builder|ItemTransfer withoutTrashed()
 * @mixin Eloquent
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
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
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
