<?php

namespace App\Events;

use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchasePurchaseInvoiceWasEmailed.
 */
class PurchaseInvoiceWasEmailed extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseInvoice
     */
    public $purchaseInvoice;

    /**
     * @var string
     */
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvoice $purchaseInvoice
     * @param $notes
     */
    public function __construct(PurchaseInvoice $purchaseInvoice, $notes)
    {
        $this->purchaseInvoice = $purchaseInvoice;
        $this->notes = $notes;
    }
}
