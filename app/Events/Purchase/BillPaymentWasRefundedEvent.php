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

    public $payment;

    public $refundAmount;

    public function __construct($payment, $refundAmount)
    {
        $this->payment = $payment;
        $this->refundAmount = $refundAmount;
    }
}
