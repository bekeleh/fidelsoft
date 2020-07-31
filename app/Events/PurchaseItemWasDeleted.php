<?php

namespace App\Events;

use App\Models\PurchaseInvoiceItem;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseItemWasCreated.
 */
class PurchaseItemWasDeleted extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseInvoiceItem
     */
    public $PurchaseItem;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvoiceItem $PurchaseItem
     */
    public function __construct(PurchaseInvoiceItem $PurchaseItem)
    {
        $this->PurchaseItem = $PurchaseItem;
    }
}
