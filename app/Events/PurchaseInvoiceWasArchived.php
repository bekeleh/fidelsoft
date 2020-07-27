<?php

namespace App\Events;

use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseInvoiceWasArchived.
 */
class PurchaseInvoiceWasArchived extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseInvoice
     */
    public $purchaseInvoice;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvoice $purchaseInvoice
     */
    public function __construct(PurchaseInvoice $purchaseInvoice)
    {
        $this->purchaseInvoice = $purchaseInvoice;
    }
}
