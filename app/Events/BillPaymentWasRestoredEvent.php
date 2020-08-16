<?php

namespace App\Events;

use App\Models\BillPayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasRestoredEvent.
 */
class BillPaymentWasRestoredEvent extends Event
{
    use SerializesModels;

    public $billPayment;
    public $fromDeleted;

    /**
     * Create a new event instance.
     *
     * @param BillPayment $billPayment
     * @param $fromDeleted
     */
    public function __construct(BillPayment $billPayment, $fromDeleted)
    {
        $this->billPayment = $billPayment;
        $this->fromDeleted = $fromDeleted;
    }
}
