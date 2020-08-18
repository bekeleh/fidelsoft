<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasRefundedEvent.
 */
class BillPaymentWasRefundedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $billPayment;

    public $refundAmount;

    public function __construct($billPayment, $refundAmount)
    {
        $this->billPayment = $billPayment;
        $this->refundAmount = $refundAmount;
    }
}
