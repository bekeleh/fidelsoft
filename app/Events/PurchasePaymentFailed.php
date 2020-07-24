<?php

namespace App\Events;

use App\Models\PurchasePayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchasePaymentFailed.
 */
class PurchasePaymentFailed extends Event
{
    use SerializesModels;

    public $purchasePayment;

    /**
     * Create a new event instance.
     *
     * @param PurchasePayment $purchasePayment
     */
    public function __construct(PurchasePayment $purchasePayment)
    {
        $this->purchasePayment = $purchasePayment;
    }
}
