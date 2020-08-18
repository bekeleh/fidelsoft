<?php

namespace App\Models\Common;

use Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class AccountGatewayToken.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $account_gateway_id
 * @property int|null $client_id
 * @property int|null $default_payment_method_id
 * @property string|null $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read AccountGateway|null $account_gateway
 * @property-read Contact|null $contact
 * @property-read PaymentMethod|null $default_payment_method
 * @property-read Collection|PaymentMethod[] $payment_methods
 * @property-read int|null $payment_methods_count
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken clientAndGateway($clientId, $accountGatewayId)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken newQuery()
 * @method static Builder|AccountGatewayToken onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereAccountGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereDefaultPaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereUserId($value)
 * @method static Builder|AccountGatewayToken withTrashed()
 * @method static Builder|AccountGatewayToken withoutTrashed()
 * @mixin Eloquent
 */
class AccountGatewayToken extends Eloquent
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'account_gateway_id',
        'client_id',
        'token',
    ];

    /**
     * @return HasMany
     */
    public function payment_methods()
    {
        return $this->hasMany('App\Models\PaymentMethod');
    }

    /**
     * @return BelongsTo
     */
    public function account_gateway()
    {
        return $this->belongsTo('App\Models\Common\AccountGateway');
    }

    /**
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo('App\Models\Contact');
    }

    /**
     * @return HasOne
     */
    public function default_payment_method()
    {
        return $this->hasOne('App\Models\PaymentMethod', 'id', 'default_payment_method_id');
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CUSTOMER;
    }

    /**
     * @return mixed
     */
    public function autoBillLater()
    {
        if ($this->default_payment_method) {
            return $this->default_payment_method->requiresDelayedAutoBill();
        }

        return false;
    }

    /**
     * @param $query
     * @param $clientId
     * @param $accountGatewayId
     *
     * @return mixed
     */
    public function scopeClientAndGateway($query, $clientId, $accountGatewayId)
    {
        $query->where('client_id', $clientId)
            ->where('account_gateway_id', $accountGatewayId);

        return $query;
    }

    /**
     * @return mixed
     */
    public function gatewayName()
    {
        return $this->account_gateway->gateway->name;
    }

    /**
     * @return bool|string
     */
    public function gatewayLink()
    {
        $accountGateway = $this->account_gateway;

        if ($accountGateway->gateway_id == GATEWAY_STRIPE) {
            return "https://dashboard.stripe.com/customers/{$this->token}";
        } elseif ($accountGateway->gateway_id == GATEWAY_BRAINTREE) {
            $merchantId = $accountGateway->getConfigField('merchantId');
            $testMode = $accountGateway->getConfigField('testMode');
            return $testMode ? "https://sandbox.braintreegateway.com/merchants/{$merchantId}/customers/{$this->token}" : "https://www.braintreegateway.com/merchants/{$merchantId}/customers/{$this->token}";
        } elseif ($accountGateway->gateway_id == GATEWAY_GOCARDLESS) {
            $testMode = $accountGateway->getConfigField('testMode');
            return $testMode ? "https://manage-sandbox.gocardless.com/customers/{$this->token}" : "https://manage.gocardless.com/customers/{$this->token}";
        } else {
            return false;
        }
    }
}
