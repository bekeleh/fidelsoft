<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillItemsWereDeletedEvent.
 */
class BillItemsWereDeletedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $Bill;


    public function __construct($Bill)
    {
        $this->Bill = $Bill;
    }
}
