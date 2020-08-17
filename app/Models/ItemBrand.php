<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ItemBrand.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $item_category_id
 * @property string|null $name
 * @property string|null $notes
 * @property int|null $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read ItemCategory|null $item_category
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand brandWithCategory()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand newQuery()
 * @method static Builder|ItemBrand onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereItemCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemBrand whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ItemBrand withTrashed()
 * @method static Builder|ItemBrand withoutTrashed()
 * @mixin Eloquent
 */
class ItemBrand extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemBrandPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $table = 'item_brands';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'item_category_id',
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [];
    protected $casts = [];
    protected $appends = [];


    public function getEntityType()
    {
        return ENTITY_ITEM_BRAND;
    }

    public function getRoute()
    {
        return "/item_brands/{$this->public_id}/edit";
    }

    public static function findItemBrandByKey($key)
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

    public function item_category()
    {
        return $this->belongsTo('App\Models\itemCategory')->withTrashed();
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->withTrashed();
    }

    public function scopeBrandWithCategory($query)
    {
        $query = $query
            ->leftJoin('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->whereNull('item_brands.deleted_at')
            ->select(
                'item_brands.id',
                'item_brands.public_id',
                'item_brands.name',
                DB::raw("COALESCE(CONCAT(NULLIF(item_brands.name,''), ' ', NULLIF(item_categories.name,'')), NULLIF(item_brands.name,'')) name")
            );

        return $query->whereNotNull('item_brands.name');
    }

}
