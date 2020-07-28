<?php

namespace App\Events;

use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseQuoteItemsWereDeleted.
 */
class PurchaseQuoteItemsWereDeleted extends Event
{
    use SerializesModels;
    public $purchaseQuote;

    /**
     * Create a new event instance.
     *
     * @param $purchaseQuote
     */
    public function __construct(PurchaseInvoice $purchaseQuote)
    {
        $this->purchaseQuote = $purchaseQuote;
    }
}
