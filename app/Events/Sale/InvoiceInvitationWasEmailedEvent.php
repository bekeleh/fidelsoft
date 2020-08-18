<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceInvitationWasEmailedEvent.
 */
class InvoiceInvitationWasEmailedEvent extends Event
{
    use SerializesModels, Dispatchable;

    public $invitation;

    /**
     * @var string
     */
    public $notes;


    public function __construct($invitation, $notes)
    {
        $this->invitation = $invitation;
        $this->notes = $notes;
    }
}
