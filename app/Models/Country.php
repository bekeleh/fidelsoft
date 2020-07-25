<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Country.
 */
class Country extends Eloquent
{

    public $timestamps = false;

    protected $visible = [
        'id',
        'name',
        'swap_postal_code',
        'swap_currency_symbol',
        'thousand_separator',
        'decimal_separator',
        'iso_3166_2',
        'iso_3166_3',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'swap_postal_code' => 'boolean',
        'swap_currency_symbol' => 'boolean',
    ];


    public function getName()
    {
        return trans('texts.country_' . $this->name);
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client')->withTrashed();
    }
}
