<?php

namespace App\Ninja\PaymentDrivers;

use App\Models\Payment;
use App\Models\Invitation;
use App\Models\PaymentMethod;
use App\Models\GatewayType;
use Illuminate\Support\Facades\Cache;
use Exception;
use App\Models\PaymentType;

class StripePaymentDriver extends BasePaymentDriver
{
    protected $customerReferenceParam = 'customerReference';
    public $canRefundPayments = true;

    public function gatewayTypes()
    {
        $types = [
            GATEWAY_TYPE_CREDIT_CARD,
            GATEWAY_TYPE_TOKEN,
        ];

        if ($gateway = $this->accountGateway) {
            $achEnabled = $gateway->getAchEnabled();
            $sofortEnabled = $gateway->getSofortEnabled();
            if ($achEnabled && $sofortEnabled) {
                if ($this->invitation) {
                    $country = ($this->client() && $this->client()->country) ? $this->client()->country->iso_3166_3 : ($this->account()->country ? $this->account()->country->iso_3166_3 : false);
                    // https://stripe.com/docs/sources/sofort
                    if ($country && in_array($country, ['AUT', 'BEL', 'DEU', 'ITA', 'NLD', 'ESP'])) {
                        $types[] = GATEWAY_TYPE_SOFORT;
                    } else {
                        $types[] = GATEWAY_TYPE_BANK_TRANSFER;
                    }
                } else {
                    $types[] = GATEWAY_TYPE_BANK_TRANSFER;
                    $types[] = GATEWAY_TYPE_SOFORT;
                }
            } elseif ($achEnabled) {
                $types[] = GATEWAY_TYPE_BANK_TRANSFER;
            } elseif ($sofortEnabled) {
                $types[] = GATEWAY_TYPE_SOFORT;
            }

            if ($gateway->getSepaEnabled()) {
                $types[] = GATEWAY_TYPE_SEPA;
            }
            if ($gateway->getBitcoinEnabled()) {
                $types[] = GATEWAY_TYPE_BITCOIN;
            }
            if ($gateway->getAlipayEnabled()) {
                $types[] = GATEWAY_TYPE_ALIPAY;
            }
            if ($gateway->getApplePayEnabled()) {
                $types[] = GATEWAY_TYPE_APPLE_PAY;
            }
        }

        return $types;
    }

    public function tokenize()
    {
        return $this->accountGateway->getPublishableKey();
    }

    public function rules()
    {
        $rules = parent::rules();

        if ($this->isGatewayType(GATEWAY_TYPE_APPLE_PAY)) {
            return ['sourceToken' => 'required'];
        }

        if ($this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER)) {
            $rules['authorize_ach'] = 'required';
        }

        return $rules;
    }

    public function isValid()
    {
        $result = $this->makeStripeCall(
            'GET',
            'charges',
            'limit=1'
        );

        if (array_get($result, 'object') == 'list') {
            return true;
        } else {
            return $result;
        }
    }

    public function shouldUseSource()
    {
        return in_array($this->gatewayType, [GATEWAY_TYPE_ALIPAY, GATEWAY_TYPE_SOFORT, GATEWAY_TYPE_BITCOIN]);
    }

    protected function checkCustomerExists($customer)
    {
        $response = $this->gateway()
            ->fetchCustomer(['customerReference' => $customer->token])
            ->send();

        if (!$response->isSuccessful()) {
            return false;
        }

        $this->tokenResponse = $response->getData();

        // import Stripe tokens created before payment methods table was added
        if (!$customer->payment_methods->count()) {
            if ($paymentMethod = $this->createPaymentMethod($customer)) {
                $customer->default_payment_method_id = $paymentMethod->id;
                $customer->save();
                $customer->load('payment_methods');
            }
        }

        return true;
    }

    public function isTwoStep()
    {
        return $this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER) && empty($this->input['plaidPublicToken']);
    }

    protected function paymentDetails($paymentMethod = false)
    {
        $data = parent::paymentDetails($paymentMethod);

        if ($paymentMethod) {
            return $data;
        }

        // Stripe complains if the email field is set
        unset($data['email']);

        if (!empty($this->input['sourceToken'])) {
            $data['token'] = $this->input['sourceToken'];
            unset($data['card']);
        }

        if (!empty($this->input['plaidPublicToken'])) {
            $data['plaidPublicToken'] = $this->input['plaidPublicToken'];
            $data['plaidAccountId'] = $this->input['plaidAccountId'];
            unset($data['card']);
        }

        return $data;
    }

    public function createToken()
    {
        $invoice = $this->invitation->invoice;
        $client = $invoice->client;

        $data = $this->paymentDetails();
        $data['description'] = $client->getDisplayName();

        // if a customer already exists link the token to it
        if ($customer = $this->customer()) {
            $data['customerReference'] = $customer->token;
            // otherwise create a new customer
        } else {
            $response = $this->gateway()->createCustomer([
                'description' => $client->getDisplayName(),
                'email' => $this->contact()->email,
            ])->send();
            $data['customerReference'] = $response->getCustomerReference();
        }

        if (!empty($data['plaidPublicToken'])) {
            $plaidResult = $this->getPlaidToken($data['plaidPublicToken'], $data['plaidAccountId']);
            unset($data['plaidPublicToken']);
            unset($data['plaidAccountId']);
            $data['token'] = $plaidResult['stripe_bank_account_token'];
        }

        $tokenResponse = $this->gateway()
            ->createCard($data)
            ->send();

        if ($tokenResponse->isSuccessful()) {
            $this->tokenResponse = $tokenResponse->getData();

            return parent::createToken();
        } else {
            throw new Exception($tokenResponse->getMessage());
        }
    }

    public function creatingCustomer($customer)
    {
        if (isset($this->tokenResponse['customer'])) {
            $customer->token = $this->tokenResponse['customer'];
        } else {
            $customer->token = $this->tokenResponse['id'];
        }

        return $customer;
    }

    protected function creatingPaymentMethod($paymentMethod)
    {
        $data = $this->tokenResponse;
        $source = false;

        if (!empty($data['object']) && ($data['object'] == 'card' || $data['object'] == 'bank_account')) {
            $source = $data;
        } elseif (!empty($data['object']) && $data['object'] == 'customer') {
            $sources = !empty($data['sources']) ? $data['sources'] : $data['cards'];
            $source = reset($sources['data']);
        } elseif (!empty($data['source'])) {
            $source = $data['source'];
        } elseif (!empty($data['card'])) {
            $source = $data['card'];
        }

        if (!$source) {
            return false;
        }

        $paymentMethod->source_reference = $source['id'];
        $paymentMethod->last4 = $source['last4'];

        // For older users the Stripe account may just have the customer token but not the card version
        // In that case we'd use GATEWAY_TYPE_TOKEN even though we're creating the credit card
        if ($this->isGatewayType(GATEWAY_TYPE_CREDIT_CARD)
            || $this->isGatewayType(GATEWAY_TYPE_APPLE_PAY)
            || $this->isGatewayType(GATEWAY_TYPE_TOKEN)) {
            if (isset($source['exp_year']) && isset($source['exp_month'])) {
                $paymentMethod->expiration = $source['exp_year'] . '-' . $source['exp_month'] . '-01';
            }
            if (isset($source['brand'])) {
                $paymentMethod->payment_type_id = PaymentType::parseCardType($source['brand']);
            }
        } elseif ($this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER)) {
            $paymentMethod->routing_number = $source['routing_number'];
            $paymentMethod->payment_type_id = PAYMENT_TYPE_ACH;
            $paymentMethod->status = $source['status'];
            $currency = Cache::get('currencies')->where('code', strtoupper($source['currency']))->first();

            if ($currency) {
                $paymentMethod->currency_id = $currency->id;
                $paymentMethod->setRelation('currency', $currency);
            }
        }

        return $paymentMethod;
    }

    protected function creatingPayment($payment, $paymentMethod)
    {
        $isBank = $this->isGatewayType(GATEWAY_TYPE_BANK_TRANSFER, $paymentMethod);
        $isAlipay = $this->isGatewayType(GATEWAY_TYPE_ALIPAY, $paymentMethod);
        $isSofort = $this->isGatewayType(GATEWAY_TYPE_SOFORT, $paymentMethod);
        $isBitcoin = $this->isGatewayType(GATEWAY_TYPE_BITCOIN, $paymentMethod);

        if ($isBank || $isAlipay || $isSofort || $isBitcoin) {
            $payment->payment_status_id = $this->purchaseResponse['status'] == 'succeeded' ? PAYMENT_STATUS_COMPLETED : PAYMENT_STATUS_PENDING;
            if ($isAlipay) {
                $payment->payment_type_id = PAYMENT_TYPE_ALIPAY;
            } elseif ($isSofort) {
                $payment->payment_type_id = PAYMENT_TYPE_SOFORT;
            } elseif ($isBitcoin) {
                $payment->payment_type_id = PAYMENT_TYPE_BITCOIN;
            }
        }

        return $payment;
    }

    public function removePaymentMethod($paymentMethod)
    {
        parent::removePaymentMethod($paymentMethod);

        if (!$paymentMethod->relationLoaded('account_gateway_token')) {
            $paymentMethod->load('account_gateway_token');
        }

        $response = $this->gateway()->deleteCard([
            'customerReference' => $paymentMethod->account_gateway_token->token,
            'cardReference' => $paymentMethod->source_reference,
        ])->send();

        if ($response->isSuccessful()) {
            return true;
        } else {
            throw new Exception($response->getMessage());
        }
    }

    private function getPlaidToken($publicToken, $accountId)
    {
        $clientId = $this->accountGateway->getPlaidClientId();
        $secret = $this->accountGateway->getPlaidSecret();

        if (!$clientId) {
            throw new Exception('plaid client id not set'); // TODO use text strings
        }

        if (!$secret) {
            throw new Exception('plaid secret not set');
        }

        try {
            $subdomain = $this->accountGateway->getPlaidEnvironment() == 'production' ? 'api' : 'tartan';
            $response = (new \GuzzleHttp\Client(['base_uri' => "https://{$subdomain}.plaid.com"]))->request(
                'POST',
                'exchange_token',
                [
                    'allow_redirects' => false,
                    'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
                    'body' => http_build_query([
                        'client_id' => $clientId,
                        'secret' => $secret,
                        'public_token' => $publicToken,
                        'account_id' => $accountId,
                    ]),
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody(), true);

            if ($body && !empty($body['message'])) {
                throw new Exception($body['message']);
            } else {
                throw new Exception($e->getMessage());
            }
        }
    }

    public function verifyBankAccount($client, $publicId, $amount1, $amount2)
    {
        $customer = $this->customer($client->id);
        $paymentMethod = PaymentMethod::clientId($client->id)
            ->wherePublicId($publicId)
            ->firstOrFail();

        // Omnipay doesn't support verifying payment methods
        // Also, it doesn't want to urlencode without putting numbers inside the brackets
        $result = $this->makeStripeCall(
            'POST',
            'customers/' . $customer->token . '/sources/' . $paymentMethod->source_reference . '/verify',
            'amounts[]=' . intval($amount1) . '&amounts[]=' . intval($amount2)
        );

        if (is_string($result) && $result != 'This bank account has already been verified.') {
            return $result;
        }

        $paymentMethod->status = PAYMENT_METHOD_STATUS_VERIFIED;
        $paymentMethod->save();

        if (!$customer->default_payment_method_id) {
            $customer->default_payment_method_id = $paymentMethod->id;
            $customer->save();
        }

        return true;
    }

    public function createSource()
    {
        $amount = intval($this->invoice()->getRequestedAmount() * 100);
        $invoiceNumber = $this->invoice()->invoice_number;
        $currency = $this->client()->getCurrencyCode();
        $email = $this->contact()->email;
        $gatewayType = GatewayType::getAliasFromId($this->gatewayType);
        $redirect = url("/complete_source/{$this->invitation->invitation_key}/{$gatewayType}");
        $country = $this->client()->country ? $this->client()->country->iso_3166_2 : ($this->account()->country ? $this->account()->country->iso_3166_2 : '');
        $extra = '';

        if ($this->gatewayType == GATEWAY_TYPE_ALIPAY) {
            if (!$this->accountGateway->getAlipayEnabled()) {
                throw new Exception('Alipay is not enabled');
            }
            $type = 'alipay';
        } elseif ($this->gatewayType == GATEWAY_TYPE_BITCOIN) {
            if (!$this->accountGateway->getBitcoinEnabled()) {
                throw new Exception('Bitcoin is not enabled');
            }
            $type = 'bitcoin';
            $extra = "&owner[email]={$email}";
        } else {
            if (!$this->accountGateway->getSofortEnabled()) {
                throw new Exception('Sofort is not enabled');
            }
            $type = 'sofort';
            $extra = "&sofort[country]={$country}&statement_descriptor={$invoiceNumber}";
        }

        $data = "type={$type}&amount={$amount}&currency={$currency}&redirect[return_url]={$redirect}{$extra}";
        $response = $this->makeStripeCall('POST', 'sources', $data);

        if (is_array($response) && isset($response['id'])) {
            $this->invitation->transaction_reference = $response['id'];
            $this->invitation->save();

            if ($this->gatewayType == GATEWAY_TYPE_BITCOIN) {
                return view('payments/stripe/bitcoin', [
                    'client' => $this->client(),
                    'account' => $this->account(),
                    'invitation' => $this->invitation,
                    'invoiceNumber' => $invoiceNumber,
                    'amount' => $this->invoice()->getRequestedAmount(),
                    'source' => $response,
                ]);
            } else {
                return redirect($response['redirect']['url']);
            }
        } else {
            throw new Exception($response);
        }
    }

    public function makeStripeCall($method, $url, $body = null)
    {
        $apiKey = $this->accountGateway->getConfig()->apiKey;

        if (!$apiKey) {
            return 'No API key set';
        }

        try {
            $options = [
                'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
                'auth' => [$apiKey, ''],
            ];

            if ($body) {
                $options['body'] = $body;
            }

            $response = (new \GuzzleHttp\Client(['base_uri' => 'https://api.stripe.com/v1/']))->request(
                $method,
                $url,
                $options
            );

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();

            $body = json_decode($response->getBody(), true);
            if ($body && $body['error'] && $body['error']['type'] == 'invalid_request_error') {
                return $body['error']['message'];
            }

            return $e->getMessage();
        }
    }

    public function handleWebHook($input)
    {
        $eventId = array_get($input, 'id');
        $eventType = array_get($input, 'type');

        $accountGateway = $this->accountGateway;
        $accountId = $accountGateway->account_id;

        if (!$eventId) {
            throw new Exception('Missing event id');
        }

        if (!$eventType) {
            throw new Exception('Missing event type');
        }

        $supportedEvents = [
            'charge.failed',
            'charge.succeeded',
            'charge.refunded',
            'customer.source.updated',
            'customer.source.deleted',
            'customer.bank_account.deleted',
            'source.chargeable',
        ];

        if (!in_array($eventType, $supportedEvents)) {
            return ['message' => 'Ignoring event'];
        }

        // Fetch the event directly from Stripe for security
        $eventDetails = $this->makeStripeCall('GET', 'events/' . $eventId);

        if (is_string($eventDetails) || !$eventDetails) {
            return false;
        }

        if ($eventType != $eventDetails['type']) {
            return false;
        }

        if (!$eventDetails['pending_webhooks']) {
            return false;
        }

        $source = $eventDetails['data']['object'];
        $sourceRef = $source['id'];

        if ($eventType == 'charge.failed' || $eventType == 'charge.succeeded' || $eventType == 'charge.refunded') {
            $payment = Payment::scope(false, $accountId)->where('transaction_reference', '=', $sourceRef)->first();

            if (!$payment) {
                return false;
            }

            if ($payment->is_deleted || $payment->invoice->is_deleted) {
                return false;
            }

            if ($eventType == 'charge.failed') {
                if (!$payment->isFailed()) {
                    $payment->markFailed($source['failure_message']);

                    $userMailer = app('App\Ninja\Mailers\UserMailer');
                    $userMailer->sendNotification($payment->user, $payment->invoice, 'payment_failed', $payment);
                }
            } elseif ($eventType == 'charge.succeeded') {
                $payment->markComplete();
            } elseif ($eventType == 'charge.refunded') {
                $payment->recordRefund($source['amount_refunded'] / 100 - $payment->refunded);
            }
        } elseif ($eventType == 'customer.source.updated' || $eventType == 'customer.source.deleted' || $eventType == 'customer.bank_account.deleted') {
            $paymentMethod = PaymentMethod::scope(false, $accountId)->where('source_reference', '=', $sourceRef)->first();

            if (!$paymentMethod) {
                return false;
            }

            if ($eventType == 'customer.source.deleted' || $eventType == 'customer.bank_account.deleted') {
                $paymentMethod->delete();
            } elseif ($eventType == 'customer.source.updated') {
                //$this->paymentService->convertPaymentMethodFromStripe($source, null, $paymentMethod)->save();
            }
        } elseif ($eventType == 'source.chargeable') {
            $this->invitation = Invitation::scope(false, $accountId)->where('transaction_reference', '=', $sourceRef)->first();
            if (!$this->invitation) {
                return false;
            }
            $data = sprintf('amount=%d&currency=%s&source=%s', $source['amount'], $source['currency'], $source['id']);
            $this->purchaseResponse = $response = $this->makeStripeCall('POST', 'charges', $data);
            $this->gatewayType = GatewayType::getIdFromAlias($source['type']);
            if (is_array($response) && isset($response['id'])) {
                $this->createPayment($response['id']);
            }
        }

        return 'Processed successfully';
    }
}
