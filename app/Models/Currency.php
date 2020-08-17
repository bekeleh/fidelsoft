<?php

namespace App\Models;

use App\Models\EntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Currency.
 *
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property string $precision
 * @property string $thousand_separator
 * @property string $decimal_separator
 * @property string $code
 * @property bool $swap_currency_symbol
 * @property float|null $exchange_rate
 * @method static Builder|Currency newModelQuery()
 * @method static Builder|Currency newQuery()
 * @method static Builder|Currency query()
 * @method static Builder|Currency whereCode($value)
 * @method static Builder|Currency whereDecimalSeparator($value)
 * @method static Builder|Currency whereExchangeRate($value)
 * @method static Builder|Currency whereId($value)
 * @method static Builder|Currency whereName($value)
 * @method static Builder|Currency wherePrecision($value)
 * @method static Builder|Currency whereSwapCurrencySymbol($value)
 * @method static Builder|Currency whereSymbol($value)
 * @method static Builder|Currency whereThousandSeparator($value)
 * @mixin \Eloquent
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
