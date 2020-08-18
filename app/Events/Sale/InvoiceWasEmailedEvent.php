<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasEmailedEvent.
 */
class InvoiceWasEmailedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $invoice;

    /**
     * @var string
     */
    public $notes;

    public function __construct($invoice, $notes)
    {
        $this->invoice = $invoice;
        $this->notes = $notes;
    }
}
