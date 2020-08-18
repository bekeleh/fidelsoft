<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentFailedEvent.
 */
class BillPaymentFailedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $billPayment;


    public function __construct($billPayment)
    {
        $this->billPayment = $billPayment;
    }
}
