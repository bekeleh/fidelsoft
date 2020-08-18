<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillWasRestoredEvent.
 */
class BillWasRestoredEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $bill;
    public $fromDeleted;


    public function __construct($bill, $fromDeleted)
    {
        $this->bill = $bill;
        $this->fromDeleted = $fromDeleted;
    }
}
