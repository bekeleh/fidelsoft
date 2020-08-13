<?php

namespace App\Events;

use App\Models\BillInvitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillInvitationWasEmailed.
 */
class BillInvitationWasEmailed extends Event
{
    use SerializesModels;

    public $billInvitation;
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
