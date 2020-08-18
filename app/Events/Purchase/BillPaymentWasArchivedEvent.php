<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\BillPayment;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillPaymentWasArchivedEvent.
 */
class BillPaymentWasArchivedEvent extends Event
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
