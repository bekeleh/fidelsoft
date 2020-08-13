<?php

namespace App\Events;

use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillWasArchived.
 */
class BillWasArchived extends Event
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
