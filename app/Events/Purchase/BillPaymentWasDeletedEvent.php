<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\BillPayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasDeletedEvent.
 */
class BillPaymentWasDeletedEvent extends Event
{
    use SerializesModels;

    public $billPayment;

    /**
     * Create a new event instance.
     *
     * @param BillPayment $billPayment
     */
    public function __construct(BillPayment $billPayment)
    {
        $this->billPayment = $billPayment;
    }
}
