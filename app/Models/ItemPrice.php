<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ItemPrice.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $client_type_id
 * @property float $unit_price
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Account|null $account
 * @property-read ClientType|null $clientType
 * @property-read Product|null $product
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice newQuery()
 * @method static Builder|ItemPrice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereClientTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ItemPrice withTrashed()
 * @method static Builder|ItemPrice withoutTrashed()
 * @mixin Eloquent
 */
class ItemPrice extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\ItemPricePresenter';

    protected $table = 'item_prices';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'product_id',
        'client_type_id',
        'unit_price',
        'start_date',
        'end_date',
        'notes',
        'is_deleted',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [];
    protected $hidden = [];
    protected $appends = [];

    public static function getImportColumns()
    {
        return [
            'name',
            'notes',
        ];
    }


    public static function getImportMap()
    {
        return [
            'name' => 'name',
            'notes|description|details' => 'notes',
        ];
    }

    public function getEntityType()
    {
        return ENTITY_ITEM_PRICE;
    }

    public static function findItemPriceByKey($key)
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

    public function product()
    {
        return $this->belongsTo('App\Models\Product')->withTrashed();
    }

    public function clientType()
    {
        return $this->belongsTo('App\Models\Setting\ClientType')->withTrashed();
    }
}
