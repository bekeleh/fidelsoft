<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillQuoteItemsWereDeletedEvent.
 */
class BillQuoteItemsWereDeletedEvent extends Event
{

    use Dispatchable, SerializesModels;

    public $quote;


    public function __construct($quote)
    {
        $this->quote = $quote;
    }
}
