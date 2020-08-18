<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasRestoredEvent.
 */
class BillQuoteWasRestoredEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $billQuote;


    public function __construct($billQuote)
    {
        $this->billQuote = $billQuote;
    }
}
