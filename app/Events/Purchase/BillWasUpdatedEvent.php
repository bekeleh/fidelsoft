<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillWasCreatedEvent.
 */
class BillWasUpdatedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Bill
     */
    public $bill;

    /**
     * Create a new event instance.
     *
     * @param Bill $bill
     */
    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }
}
