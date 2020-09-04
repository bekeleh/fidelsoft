<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasArchivedEvent.
 */
class BillPaymentWasArchivedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $payment;


    public function __construct($payment)
    {
        $this->payment = $payment;
    }
}
