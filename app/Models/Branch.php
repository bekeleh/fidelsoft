<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Branch.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int|null $user_id
 * @property int|null $company_id
 * @property int|null $location_id
 * @property int|null $warehouse_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|Bill[] $bills
 * @property-read int|null $bills_count
 * @property-read Collection|Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read Collection|ItemRequest[] $itemRequest
 * @property-read int|null $item_request_count
 * @property-read Location|null $location
 * @property-read User $manager
 * @property-read User|null $user
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @property-read Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newQuery()
 * @method static Builder|Branch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Branch withTrashed()
 * @method static Builder|Branch withoutTrashed()
 * @mixin Eloquent
 */
class Branch extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\BranchPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'warehouse_id',
        'location_id',
        'company_id',
        'is_deleted',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_BRANCH;
    }

    public function getRoute()
    {
        return "/branches/{$this->public_id}";
    }

    public function getUpperAttributes()
    {
        return strtoupper($this->name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    public static function findBranchByKey($key)
    {
        return self::scope()->where('name', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function users()
    {
        return $this->hasMany('App\Models\User')->withTrashed();
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice')->withTrashed();
    }

    public function bills()
    {
        return $this->hasMany('App\Models\Bill')->withTrashed();
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse')->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location')->withTrashed();
    }

//    public function warehouses()
//    {
//        return $this->hasMany('App\Models\Warehouse')->withTrashed();
//    }

    public function itemRequest()
    {
        return $this->hasMany('App\Models\ItemRequest')->withTrashed();
    }

}
