<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillItemsWereCreatedEvent.
 */
class BillItemsWereCreatedEvent extends Event
{
    use Dispatchable, Queueable, SerializesModels;

    public $bill;


    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }
}
