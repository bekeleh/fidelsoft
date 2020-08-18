<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasArchivedEvent.
 */
class InvoiceWasArchivedEvent extends Event
{
    use SerializesModels, Dispatchable;


    public $invoice;


    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }
}
