<?php

namespace App\Events;

use App\Models\PurchasePayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchasePaymentCompleted.
 */
class PurchasePaymentCompleted extends Event
{
    use SerializesModels;

    /**
     * @var PurchasePayment
     */
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
