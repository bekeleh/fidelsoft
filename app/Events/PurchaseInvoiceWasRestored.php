<?php

namespace App\Events;

use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseInvoiceWasRestored.
 */
class PurchaseInvoiceWasRestored extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseInvoice
     */
    public $purchaseInvoice;

    public $fromDeleted;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvoice $purchaseInvoice
     * @param $fromDeleted
     */
    public function __construct(PurchaseInvoice $purchaseInvoice, $fromDeleted)
    {
        $this->purchaseInvoice = $purchaseInvoice;
        $this->fromDeleted = $fromDeleted;
    }
}
