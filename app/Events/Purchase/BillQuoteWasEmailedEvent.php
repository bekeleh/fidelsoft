<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasEmailedEvent.
 */
class BillQuoteWasEmailedEvent extends Event
{
    use SerializesModels;

    public $billQuote;

    /**
     * @var string
     */
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param $billQuote
     * @param $notes
     */
    public function __construct(Bill $billQuote, $notes)
    {
        $this->billQuote = $billQuote;
        $this->notes = $notes;
    }
}
