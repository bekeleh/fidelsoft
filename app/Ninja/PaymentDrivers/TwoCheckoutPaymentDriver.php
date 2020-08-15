<?php

namespace App\Ninja\PaymentDrivers;

class TwoCheckoutPaymentDriver extends BasePaymentDriver
{
    protected $transactionReferenceParam = 'cart_order_id';

    // Calling completeBill results in an 'invalid key' error
    public function completeOffsiteBill($input)
    {
        return $this->createPayment($input['order_number']);
    }
}
