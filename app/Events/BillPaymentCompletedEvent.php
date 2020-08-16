<?php

namespace App\Events;

use App\Models\BillPayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentCompletedEvent.
 */
class BillPaymentCompletedEvent extends Event
{
    use SerializesModels;

    /**
     * @var BillPayment
     */
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
