<?php

namespace App\Events;

use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseInvoiceWasCreated.
 */
class PurchaseInvoiceWasUpdated extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseInvoice
     */
    public $PurchaseInvoice;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvoice $PurchaseInvoice
     */
    public function __construct(PurchaseInvoice $PurchaseInvoice)
    {
        $this->PurchaseInvoice = $PurchaseInvoice;
    }
}
