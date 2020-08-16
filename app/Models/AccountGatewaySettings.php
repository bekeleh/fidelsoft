<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Utils;

/**
 * Class AccountGatewaySettings.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $gateway_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $min_limit
 * @property int|null $max_limit
 * @property float|null $fee_amount
 * @property float|null $fee_percent
 * @property string|null $fee_tax_name1
 * @property string|null $fee_tax_name2
 * @property float|null $fee_tax_rate1
 * @property float|null $fee_tax_rate2
 * @property-read GatewayType|null $gatewayType
 * @method static Builder|AccountGatewaySettings newModelQuery()
 * @method static Builder|AccountGatewaySettings newQuery()
 * @method static Builder|AccountGatewaySettings query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|AccountGatewaySettings whereAccountId($value)
 * @method static Builder|AccountGatewaySettings whereCreatedAt($value)
 * @method static Builder|AccountGatewaySettings whereDeletedAt($value)
 * @method static Builder|AccountGatewaySettings whereFeeAmount($value)
 * @method static Builder|AccountGatewaySettings whereFeePercent($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxName1($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxName2($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxRate1($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxRate2($value)
 * @method static Builder|AccountGatewaySettings whereGatewayTypeId($value)
 * @method static Builder|AccountGatewaySettings whereId($value)
 * @method static Builder|AccountGatewaySettings whereMaxLimit($value)
 * @method static Builder|AccountGatewaySettings whereMinLimit($value)
 * @method static Builder|AccountGatewaySettings wherePublicId($value)
 * @method static Builder|AccountGatewaySettings whereUpdatedAt($value)
 * @method static Builder|AccountGatewaySettings whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
class AccountGatewaySettings extends EntityModel
{
    /**
     * @var array
     */
    protected $dates = ['updated_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'fee_amount',
        'fee_percent',
        'fee_tax_name1',
        'fee_tax_rate1',
        'fee_tax_name2',
        'fee_tax_rate2',
    ];

    /**
     * @var bool
     */
    protected static $hasPublicId = false;

    /**
     * @return BelongsTo
     */
    public function gatewayType()
    {
        return $this->belongsTo('App\Models\GatewayType');
    }

    public function setCreatedAtAttribute($value)
    {
        // to Disable created_at
    }

    public function areFeesEnabled()
    {
        return floatval($this->fee_amount) || floatval($this->fee_percent);
    }

    public function hasTaxes()
    {
        return floatval($this->fee_tax_rate1) || floatval($this->fee_tax_rate1);
    }

    public function feesToString()
    {
        $parts = [];

        if (floatval($this->fee_amount) != 0) {
            $parts[] = Utils::formatMoney($this->fee_amount);
        }

        if (floatval($this->fee_percent) != 0) {
            $parts[] = (floor($this->fee_percent * 1000) / 1000) . '%';
        }

        return join(' + ', $parts);
    }
}
