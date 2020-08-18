<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillItemsWereDeletedEvent.
 */
class BillItemsWereDeletedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Bill
     */
    public $Bill;

    /**
     * Create a new event instance.
     *
     * @param Bill $Bill
     */
    public function __construct(Bill $Bill)
    {
        $this->Bill = $Bill;
    }
}
