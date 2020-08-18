<?php

namespace App\Events\Sale;

use App\Events\Event;
use App\Models\Invitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteInvitationWasViewedEvent.
 */
class QuoteInvitationWasViewedEvent extends Event
{
    use SerializesModels;

    public $quote;

    /**
     * @var Invitation
     */
    public $invitation;

    /**
     * Create a new event instance.
     *
     * @param $quote
     * @param Invitation $invitation
     */
    public function __construct($quote, Invitation $invitation)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
