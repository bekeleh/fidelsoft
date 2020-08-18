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

    public $billPayment;
    public $fromDeleted;


    public function __construct($billPayment, $fromDeleted)
    {
        $this->billPayment = $billPayment;
        $this->fromDeleted = $fromDeleted;
    }
}
