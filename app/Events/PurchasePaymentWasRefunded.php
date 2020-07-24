<?php

namespace App\Events;

use App\Models\PurchasePayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchasePaymentWasRefunded.
 */
class PurchasePaymentWasRefunded extends Event
{
    use SerializesModels;

    public $purchasePayment;

    public $refundAmount;

    /**
     * Create a new event instance.
     *
     * @param PurchasePayment $purchasePayment
     * @param $refundAmount
     */
    public function __construct(PurchasePayment $purchasePayment, $refundAmount)
    {
        $this->purchasePayment = $purchasePayment;
        $this->refundAmount = $refundAmount;
    }
}
