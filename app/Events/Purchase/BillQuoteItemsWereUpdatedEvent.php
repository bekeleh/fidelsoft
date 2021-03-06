<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteItemsWereUpdatedEvent.
 */
class BillQuoteItemsWereUpdatedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }
}
