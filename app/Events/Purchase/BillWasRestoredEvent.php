<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillWasRestoredEvent.
 */
class BillWasRestoredEvent extends Event
{
    use SerializesModels;

    /**
     * @var Bill
     */
    public $bill;

    public $fromDeleted;

    /**
     * Create a new event instance.
     *
     * @param Bill $bill
     * @param $fromDeleted
     */
    public function __construct(Bill $bill, $fromDeleted)
    {
        $this->bill = $bill;
        $this->fromDeleted = $fromDeleted;
    }
}
