<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasRestoredEvent.
 */
class BillPaymentWasRestoredEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $payment;
    public $fromDeleted;


    public function __construct($payment, $fromDeleted)
    {
        $this->payment = $payment;
        $this->fromDeleted = $fromDeleted;
    }
}
