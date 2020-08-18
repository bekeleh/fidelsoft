<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasRestoredEvent.
 */
class InvoiceWasRestoredEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $invoice;

    public $fromDeleted;

    public function __construct($invoice, $fromDeleted)
    {
        $this->invoice = $invoice;
        $this->fromDeleted = $fromDeleted;
    }
}
