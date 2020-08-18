<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\BillPayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasRefundedEvent.
 */
class BillPaymentWasRefundedEvent extends Event
{
    use SerializesModels;

    public $billPayment;

    public $refundAmount;

    /**
     * Create a new event instance.
     *
     * @param BillPayment $billPayment
     * @param $refundAmount
     */
    public function __construct(BillPayment $billPayment, $refundAmount)
    {
        $this->billPayment = $billPayment;
        $this->refundAmount = $refundAmount;
    }
}
