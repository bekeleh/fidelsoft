<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Category.
 */
class TaxCategory extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var bool
     */
    protected $softDelete = true;

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.tax_category_' . Str::slug($this->name, '_'));
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product')->withTrashed();
    }
}
