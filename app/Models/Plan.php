<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Category.
 */
class Plan extends Eloquent
{

    public $timestamps = true;

    protected $softDelete = true;

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.plan_' . Str::slug($this->name, '_'));
    }
}
