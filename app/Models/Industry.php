<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Industry.
 */
class Industry extends Eloquent
{

    public $timestamps = false;

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.industry_' . Str::slug($this->name, '_'));
    }

}
