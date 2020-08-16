<?php

namespace App\Events;

use App\Models\BillPayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasCreatedEvent.
 */
class BillPaymentWasCreatedEvent extends Event
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
