<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasCreatedEvent.
 */
class QuoteWasCreatedEvent extends Event
{
    use SerializesModels;
    public $quote;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct($quote)
    {
        $this->quote = $quote;
    }
}
