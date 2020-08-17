<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceItemsWereUpdatedEvent.
 */
class InvoiceItemsWereUpdatedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $invoice;

    /**
     * Create a new event instance.
     *
     * @param $invoice
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }
}
