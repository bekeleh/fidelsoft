<?php

namespace App\Models\Common;

use App\Libraries\Utils;
use App\Models\EntityModel;
use Illuminate\Database\Eloquent\Model as Eloquent;
use HTMLUtils;
use Crypt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;
use URL;

/**
 * Class AccountGateway.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $gateway_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $config
 * @property int|null $accepted_credit_cards
 * @property int $show_address
 * @property int $update_address
 * @property int $require_cvv
 * @property int $show_shipping_address
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Gateway|null $gateway
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway newQuery()
 * @method static Builder|AccountGateway onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereAcceptedCreditCards($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereRequireCvv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereShowAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereShowShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereUpdateAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|AccountGateway withTrashed()
 * @method static Builder|AccountGateway withoutTrashed()
 * @mixin Eloquent
 */
class AccountGateway extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;


    protected $presenter = 'App\Ninja\Presenters\AccountGatewayPresenter';

    protected $dates = ['created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $hidden = [
        'config',
        'deleted_at'
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_ACCOUNT_GATEWAY;
    }

    /**
     * @return BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }

    /**
     * @return array
     */
    public function getCreditcardTypes()
    {
        $flags = unserialize(CREDIT_CARDS);
        $arrayOfImages = [];

        foreach ($flags as $card => $name) {
            if (($this->accepted_credit_cards & $card) == $card) {
                $arrayOfImages[] = ['source' => asset($name['card']), 'alt' => $name['text']];
            }
        }

        return $arrayOfImages;
    }

    /**
     * @param $provider
     *
     * @return string
     */
    public static function paymentDriverClass($provider)
    {
        $folder = 'App\\Ninja\\PaymentDrivers\\';
        $provider = str_replace('\\', '', $provider);
        $class = $folder . $provider . 'PaymentDriver';
        $class = str_replace('_', '', $class);

        if (class_exists($class)) {
            return $class;
        } else {
            return $folder . 'BasePaymentDriver';
        }
    }

    /**
     * @param bool $invitation
     * @param mixed $gatewayTypeId
     *
     * @return mixed
     */
    public function paymentDriver($invitation = false, $gatewayTypeId = false)
    {
        $class = static::paymentDriverClass($this->gateway->provider);

        return new $class($this, $invitation, $gatewayTypeId);
    }

    /**
     * @param $gatewayId
     *
     * @return bool
     */
    public function isGateway($gatewayId)
    {
        if (is_array($gatewayId)) {
            foreach ($gatewayId as $id) {
                if ($this->gateway_id == $id) {
                    return true;
                }
            }
            return false;
        } else {
            return $this->gateway_id == $gatewayId;
        }
    }

    public function isCustom()
    {
        return in_array($this->gateway_id, [GATEWAY_CUSTOM1, GATEWAY_CUSTOM2, GATEWAY_CUSTOM3]);
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = Crypt::encrypt(json_encode($config));
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return json_decode(Crypt::decrypt($this->config));
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function getConfigField($field)
    {
        return object_get($this->getConfig(), $field, false);
    }

    /**
     * @return bool|mixed
     */
    public function getPublishableKey()
    {
        if (!$this->isGateway([GATEWAY_STRIPE, GATEWAY_PAYMILL])) {
            return false;
        }

        return $this->getConfigField('publishableKey');
    }

    public function getAppleMerchantId()
    {
        if (!$this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('appleMerchantId');
    }

    /**
     * @return bool
     */
    public function getAchEnabled()
    {
        return !empty($this->getConfigField('enableAch'));
    }

    /**
     * @return bool
     */
    public function getApplePayEnabled()
    {
        return !empty($this->getConfigField('enableApplePay'));
    }

    /**
     * @return bool
     */
    public function getAlipayEnabled()
    {
        return !empty($this->getConfigField('enableAlipay'));
    }

    /**
     * @return bool
     */
    public function getSofortEnabled()
    {
        return !empty($this->getConfigField('enableSofort'));
    }

    /**
     * @return bool
     */
    public function getSepaEnabled()
    {
        return !empty($this->getConfigField('enableSepa'));
    }

    /**
     * @return bool
     */
    public function getBitcoinEnabled()
    {
        return !empty($this->getConfigField('enableBitcoin'));
    }

    /**
     * @return bool
     */
    public function getPayPalEnabled()
    {
        return !empty($this->getConfigField('enablePayPal'));
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidSecret()
    {
        if (!$this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidSecret');
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidClientId()
    {
        if (!$this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidClientId');
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidPublicKey()
    {
        if (!$this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidPublicKey');
    }

    /**
     * @return bool
     */
    public function getPlaidEnabled()
    {
        return !empty($this->getPlaidClientId()) && $this->getAchEnabled();
    }

    /**
     * @return null|string
     */
    public function getPlaidEnvironment()
    {
        if (!$this->getPlaidClientId()) {
            return null;
        }

        $stripe_key = $this->getPublishableKey();

        return substr(trim($stripe_key), 0, 8) == 'pk_test_' ? 'tartan' : 'production';
    }

    /**
     * @return string
     */
    public function getWebhookUrl()
    {
        $account = $this->account ? $this->account : Account::find($this->account_id);

        return URL::to(env('WEBHOOK_PREFIX', '') . 'payment_hook/' . $account->account_key . '/' . $this->gateway_id . env('WEBHOOK_SUFFIX', ''));
    }

    public function isTestMode()
    {
        if ($this->isGateway(GATEWAY_STRIPE)) {
            return strpos($this->getPublishableKey(), 'test') !== false;
        } else {
            return $this->getConfigField('testMode');
        }
    }

    public function getCustomHtml($invitation)
    {
        $text = $this->getConfigField('text');

        if ($text == strip_tags($text)) {
            $text = nl2br($text);
        }

        if (Utils::isNinja()) {
            $text = HTMLUtils::sanitizeHTML($text);
        }

        $templateService = app('App\Services\TemplateService');
        $text = $templateService->processVariables($text, ['invitation' => $invitation]);

        return $text;
    }
}
