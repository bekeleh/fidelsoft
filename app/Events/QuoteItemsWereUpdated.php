<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteItemsWereUpdated.
 */
class QuoteItemsWereUpdated extends Event
{
    use SerializesModels;
    public $quote;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct(Invoice $quote)
    {
        $this->quote = $quote;
    }
}
