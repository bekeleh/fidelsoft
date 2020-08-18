<?php

namespace App\Events\Sale;

use App\Events\Event;
use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasRestoredEvent.
 */
class InvoiceWasRestoredEvent extends Event
{
    use SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;
    
    public $fromDeleted;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     * @param $fromDeleted
     */
    public function __construct(Invoice $invoice, $fromDeleted)
    {
        $this->invoice = $invoice;
        $this->fromDeleted = $fromDeleted;
    }
}
