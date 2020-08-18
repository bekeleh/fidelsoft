<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillInvitationWasEmailedEvent.
 */
class BillInvitationWasEmailedEvent extends Event
{
    use Dispatchable, Queueable, SerializesModels;

    public $billInvitation;
    public $notes;

    public function __construct($billInvitation, $notes)
    {
        $this->billInvitation = $billInvitation;
        $this->notes = $notes;
    }
}
