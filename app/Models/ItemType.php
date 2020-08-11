<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class ItemType.
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
