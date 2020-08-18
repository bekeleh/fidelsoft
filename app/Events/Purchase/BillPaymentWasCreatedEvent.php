<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasCreatedEvent.
 */
class BillPaymentWasCreatedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $billPayment;

    public function __construct($billPayment)
    {
        $this->billPayment = $billPayment;
    }
}
