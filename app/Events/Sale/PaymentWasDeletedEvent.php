<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentWasDeletedEvent.
 */
class PaymentWasDeletedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $payment;


    public function __construct($payment)
    {
        $this->payment = $payment;
    }
}
