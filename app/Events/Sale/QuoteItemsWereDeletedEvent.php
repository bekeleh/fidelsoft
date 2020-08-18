<?php

namespace App\Events\Sale;

use App\Events\Event;
use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteItemsWereUpdatedEvent.
 */
class QuoteItemsWereDeletedEvent extends Event
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
