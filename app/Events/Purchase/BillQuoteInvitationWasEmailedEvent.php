<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\BillInvitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillQuoteInvitationWasEmailedEvent.
 */
class BillQuoteInvitationWasEmailedEvent extends Event
{
    use SerializesModels;

    /**
     * @var BillInvitation
     */
    public $billInvitation;

    /**
     * @var string
     */
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param BillInvitation $billInvitation
     * @param mixed $notes
     */
    public function __construct(BillInvitation $billInvitation, $notes)
    {
        $this->billInvitation = $billInvitation;
        $this->notes = $notes;
    }
}
