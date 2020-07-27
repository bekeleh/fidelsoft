<?php

namespace App\Events;

use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

class PurchaseQuoteWasArchived extends Event
{
    use SerializesModels;
    public $quote;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct(PurchaseInvoice $quote)
    {
        $this->quote = $quote;
    }
}
