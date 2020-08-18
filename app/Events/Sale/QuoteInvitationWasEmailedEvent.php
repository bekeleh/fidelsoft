<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteInvitationWasEmailedEvent.
 */
class QuoteInvitationWasEmailedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $invitation;
    public $notes;


    public function __construct($invitation, $notes)
    {
        $this->invitation = $invitation;
        $this->notes = $notes;
    }
}
