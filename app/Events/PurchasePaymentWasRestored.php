<?php

namespace App\Events;

use App\Models\PurchasePayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchasePaymentWasRestored.
 */
class PurchasePaymentWasRestored extends Event
{
    use SerializesModels;

    public $purchasePayment;
    public $fromDeleted;

    /**
     * Create a new event instance.
     *
     * @param PurchasePayment $purchasePayment
     * @param $fromDeleted
     */
    public function __construct(PurchasePayment $purchasePayment, $fromDeleted)
    {
        $this->purchasePayment = $purchasePayment;
        $this->fromDeleted = $fromDeleted;
    }
}
