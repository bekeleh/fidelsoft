<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Currency.
 */
class Currency extends Eloquent
{

    public $timestamps = false;

    protected $casts = [
        'swap_currency_symbol' => 'boolean',
        'exchange_rate' => 'double',
    ];


    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.currency_' . Str::slug($this->name, '_'));
    }

}
