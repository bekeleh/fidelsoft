<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillItemsWereCreatedEvent.
 */
class BillItemsWereCreatedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $bill;


    public function __construct($bill)
    {
        $this->bill = $bill;
    }
}
