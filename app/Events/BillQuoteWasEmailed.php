<?php

namespace App\Events;

use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasEmailed.
 */
class BillQuoteWasEmailed extends Event
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
