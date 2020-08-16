<?php

namespace App\Events;

use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillBillWasEmailed.
 */
class BillWasEmailedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Bill
     */
    public $bill;

    /**
     * @var string
     */
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param Bill $bill
     * @param $notes
     */
    public function __construct(Bill $bill, $notes)
    {
        $this->bill = $bill;
        $this->notes = $notes;
    }
}
