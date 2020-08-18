<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasDeletedEvent.
 */
class QuoteWasDeletedEvent extends Event
{
    use Dispatchable, SerializesModels;
    public $quote;

    public function __construct($quote)
    {
        $this->quote = $quote;
    }
}
