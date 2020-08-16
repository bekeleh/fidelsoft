<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ItemStore.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $warehouse_id
 * @property string|null $bin
 * @property float|null $qty
 * @property int|null $EOQ
 * @property int|null $reorder_level
 * @property int $is_locked
 * @property int $is_public
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Product|null $product
 * @property-read Collection|ItemMovement[] $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read User|null $user
 * @property-read Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore newQuery()
 * @method static Builder|ItemStore onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereBin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereEOQ($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereIsLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereReorderLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemStore whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ItemStore withTrashed()
 * @method static Builder|ItemStore withoutTrashed()
 * @mixin Eloquent
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
