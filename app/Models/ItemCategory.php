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
 * Model Class ItemCategory.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|ItemBrand[] $item_brands
 * @property-read int|null $item_brands_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory newQuery()
 * @method static Builder|ItemCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemCategory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|ItemCategory withTrashed()
 * @method static Builder|ItemCategory withoutTrashed()
 * @mixin Eloquent
 */
class ItemCategory extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ItemCategoryPresenter';
    use PresentableTrait;
    use SoftDeletes;


    protected $table = 'item_categories';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];
    protected $appends = [];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_ITEM_CATEGORY;
    }

    public function getRoute()
    {
        return "/item_categories/{$this->public_id}/edit";
    }

    public static function findItemCategoryByKey($key)
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

    public function item_brands()
    {
        return $this->hasMany('App\Models\ItemBrand')->withTrashed();
    }

    public static function selectOptions()
    {
        $categories = ItemCategory::where('account_id', null)->get();

        foreach (TaxCategory::scope()->get() as $category) {
            $categories->push($category);
        }

        foreach($categories as $category){
            $name = Str::snake(str_replace(' ', '_', $category->name));
            $categories->name = trans('texts.item_category_' . $name);
        }

        return $categories->sortBy('name');
    }

}
