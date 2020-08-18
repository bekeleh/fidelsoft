<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceItemsWereCreatedEvent.
 */
class InvoiceItemsWereCreatedEvent extends Event
{
    use SerializesModels, Dispatchable;


    public $invoice;


    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }
}
