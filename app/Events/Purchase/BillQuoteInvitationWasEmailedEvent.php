<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillQuoteInvitationWasEmailedEvent.
 */
class BillQuoteInvitationWasEmailedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $billInvitation;
    public $notes;

    public function __construct($billInvitation, $notes)
    {
        $this->billInvitation = $billInvitation;
        $this->notes = $notes;
    }
}
