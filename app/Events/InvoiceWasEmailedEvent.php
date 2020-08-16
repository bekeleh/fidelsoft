<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasEmailedEvent.
 */
class InvoiceWasEmailedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * @var string
     */
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice, $notes)
    {
        $this->invoice = $invoice;
        $this->notes = $notes;
    }
}
