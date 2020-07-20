<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasEmailed.
 */
class PurchaseQuoteWasEmailed extends Event
{
    use SerializesModels;
    public $purchaseQuote;

    /**
     * @var string
     */
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param $purchaseQuote
     * @param $notes
     */
    public function __construct($purchaseQuote, $notes)
    {
        $this->purchaseQuote = $purchaseQuote;
        $this->notes = $notes;
    }
}
