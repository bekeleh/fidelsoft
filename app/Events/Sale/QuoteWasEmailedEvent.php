<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasEmailedEvent.
 */
class QuoteWasEmailedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $quote;
    public $notes;


    public function __construct($quote, $notes)
    {
        $this->quote = $quote;
        $this->notes = $notes;
    }
}
