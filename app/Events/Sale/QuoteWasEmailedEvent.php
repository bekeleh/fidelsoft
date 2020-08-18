<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasEmailedEvent.
 */
class QuoteWasEmailedEvent extends Event
{
    use SerializesModels;
    public $quote;

    /**
     * @var string
     */
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct($quote, $notes)
    {
        $this->quote = $quote;
        $this->notes = $notes;
    }
}
