<?php

namespace App\Models;

use App\Models\EntityModel;
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

    public static function selectOptions()
    {
        $categories = TaxCategory::where('account_id', null)->get();

        foreach (TaxCategory::scope()->get() as $category) {
            $categories->push($category);
        }

        foreach($categories as $category){
            $name = Str::snake(str_replace(' ', '_', $category->name));
            $categories->name = trans('texts.tax_category_' . $name);
        }

        return $categories->sortBy('name');
    }

    
}
