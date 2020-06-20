<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Category.
 */
class Category extends Eloquent
{

    public $timestamps = true;

    protected $softDelete = true;

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.category_' . Str::slug($this->name, '_'));
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->withTrashed();
    }
}
