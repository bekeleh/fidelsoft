<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use App\Events\ItemRequestWasCreatedEvent;
use App\Events\ItemRequestWasUpdatedEvent;

/**
 * Model Class ItemRequestPresenter.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $department_id
 * @property int|null $warehouse_id
 * @property int|null $status_id
 * @property int|null $qty
 * @property int|null $delivered_qty
 * @property string|null $required_date
 * @property string|null $dispatch_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Department|null $department
 * @property-read Product|null $product
 * @property-read Status|null $status
 * @property-read User|null $user
 * @property-read Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest newQuery()
 * @method static Builder|ItemRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereDeliveredQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereDispatchDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereRequiredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemRequest whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ItemRequest withTrashed()
 * @method static Builder|ItemRequest withoutTrashed()
 * @mixin Eloquent
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
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
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
    event(new ItemRequestWasCreatedEvent($itemRequest));
});

ItemRequest::updating(function ($itemRequest) {
    event(new ItemRequestWasUpdatedEvent($itemRequest));
});
