<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteInvitationWasViewedEvent.
 */
class QuoteInvitationWasViewedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $quote;
    public $invitation;


    public function __construct($quote, $invitation)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
