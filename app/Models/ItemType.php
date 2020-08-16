<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class ItemType.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int|null $user_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @method static Builder|ItemType newModelQuery()
 * @method static Builder|ItemType newQuery()
 * @method static Builder|ItemType query()
 * @method static Builder|ItemType whereAccountId($value)
 * @method static Builder|ItemType whereCreatedAt($value)
 * @method static Builder|ItemType whereDeletedAt($value)
 * @method static Builder|ItemType whereId($value)
 * @method static Builder|ItemType whereName($value)
 * @method static Builder|ItemType wherePublicId($value)
 * @method static Builder|ItemType whereUpdatedAt($value)
 * @method static Builder|ItemType whereUserId($value)
 * @mixin \Eloquent
 */
class ItemType extends Eloquent
{

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.item_type_' . Str::slug($this->name, '_'));
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->withTrashed();
    }

    public static function selectOptions()
    {
        $types = ItemType::where('account_id', null)->get();

        foreach (ItemType::scope()->get() as $type) {
            $types->push($type);
        }

        foreach($types as $type){
            $name = Str::snake(str_replace(' ', '_', $type->name));
            $types->name = trans('texts.item_type_' . $name);
        }

        return $types->sortBy('name');
    }
}
