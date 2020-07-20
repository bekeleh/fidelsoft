<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseQuoteWasCreated.
 */
class PurchaseQuoteWasCreated extends Event
{
    use SerializesModels;
    public $purchaseQuote;

    /**
     * Create a new event instance.
     *
     * @param $purchaseQuote
     */
    public function __construct($purchaseQuote)
    {
        $this->purchaseQuote = $purchaseQuote;
    }
}
