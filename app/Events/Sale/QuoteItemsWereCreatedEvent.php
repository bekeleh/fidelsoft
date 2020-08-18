<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteItemsWereCreatedEvent.
 */
class QuoteItemsWereCreatedEvent extends Event
{
    use Dispatchable, SerializesModels;
    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }
}
