<?php

namespace App\Models;

use App\Models\EntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Country.
 *
 * @property int $id
 * @property string|null $capital
 * @property string|null $citizenship
 * @property string|null $country_code
 * @property string|null $currency
 * @property string|null $currency_code
 * @property string|null $currency_sub_unit
 * @property string|null $full_name
 * @property string|null $iso_3166_2
 * @property string|null $iso_3166_3
 * @property string|null $name
 * @property string|null $region_code
 * @property string|null $sub_region_code
 * @property int $eea
 * @property bool $swap_postal_code
 * @property bool $swap_currency_symbol
 * @property string|null $thousand_separator
 * @property string|null $decimal_separator
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @method static Builder|Country newModelQuery()
 * @method static Builder|Country newQuery()
 * @method static Builder|Country query()
 * @method static Builder|Country whereCapital($value)
 * @method static Builder|Country whereCitizenship($value)
 * @method static Builder|Country whereCountryCode($value)
 * @method static Builder|Country whereCreatedAt($value)
 * @method static Builder|Country whereCreatedBy($value)
 * @method static Builder|Country whereCurrency($value)
 * @method static Builder|Country whereCurrencyCode($value)
 * @method static Builder|Country whereCurrencySubUnit($value)
 * @method static Builder|Country whereDecimalSeparator($value)
 * @method static Builder|Country whereDeletedAt($value)
 * @method static Builder|Country whereDeletedBy($value)
 * @method static Builder|Country whereEea($value)
 * @method static Builder|Country whereFullName($value)
 * @method static Builder|Country whereId($value)
 * @method static Builder|Country whereIso31662($value)
 * @method static Builder|Country whereIso31663($value)
 * @method static Builder|Country whereName($value)
 * @method static Builder|Country whereRegionCode($value)
 * @method static Builder|Country whereSubRegionCode($value)
 * @method static Builder|Country whereSwapCurrencySymbol($value)
 * @method static Builder|Country whereSwapPostalCode($value)
 * @method static Builder|Country whereThousandSeparator($value)
 * @method static Builder|Country whereUpdatedAt($value)
 * @method static Builder|Country whereUpdatedBy($value)
 * @mixin \Eloquent
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
