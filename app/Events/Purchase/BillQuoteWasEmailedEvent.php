<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasEmailedEvent.
 */
class BillQuoteWasEmailedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $billQuote;
    public $notes;


    public function __construct($billQuote, $notes)
    {
        $this->billQuote = $billQuote;
        $this->notes = $notes;
    }
}
