<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillBillWasEmailed.
 */
class BillWasEmailedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $bill;
    public $notes;


    public function __construct($bill, $notes)
    {
        $this->bill = $bill;
        $this->notes = $notes;
    }
}
