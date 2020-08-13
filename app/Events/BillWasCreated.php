<?php

namespace App\Events;

use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillWasCreated.
 */
class BillWasCreated extends Event
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
