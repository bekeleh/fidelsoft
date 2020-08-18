<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillItemWasCreatedEvent.
 */
class BillItemWasCreatedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $billItem;


    public function __construct($billItem)
    {
        $this->billItem = $billItem;
    }
}
