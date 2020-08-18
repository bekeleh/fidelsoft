<?php

namespace App\Events\Sale;

use App\Events\Event;
use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasCreatedEvent.
 */
class InvoiceWasCreatedEvent extends Event
{
    use SerializesModels;

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
