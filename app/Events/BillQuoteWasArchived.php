<?php

namespace App\Events;

use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

class BillQuoteWasArchived extends Event
{
    use SerializesModels;

    public $billQuote;

    /**
     * Create a new event instance.
     *
     * @param $billQuote
     */
    public function __construct(Bill $billQuote)
    {
        $this->billQuote = $billQuote;
    }
}
