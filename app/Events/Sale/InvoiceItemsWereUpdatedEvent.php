<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


/**
 * Class InvoiceItemsWereUpdatedEvent.
 */
class InvoiceItemsWereUpdatedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $invoice;


    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }
}
