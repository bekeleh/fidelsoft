<?php

namespace App\Events;

use App\Models\Invitation;
use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

class QuoteInvitationWasApprovedEvent extends Event
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
     * @param Invoice $quote
     * @param Invitation $invitation
     */
    public function __construct(Invoice $quote, Invitation $invitation)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
