<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasUpdated.
 */
class InvoiceWasUpdated extends Event
{
    use Dispatchable, SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
