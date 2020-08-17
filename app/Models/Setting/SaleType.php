<?php

namespace App\Models\Setting;

use App\Models\Common\EntityModel;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Product.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read Collection|ItemPrice[] $itemPrices
 * @property-read int|null $item_prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType newQuery()
 * @method static Builder|SaleType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleType whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|SaleType withTrashed()
 * @method static Builder|SaleType withoutTrashed()
 * @mixin Eloquent
 */
class SaleType extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\SaleTypePresenter';

    protected $table = 'sale_types';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

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
        return ENTITY_SALE_TYPE;
    }

    public function itemPrices()
    {
        return $this->hasMany('App\Models\ItemPrice');
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client')->withTrashed();
    }
}
