<?php

namespace App\Events;

use App\Models\BillInvitation;
use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillInvitationWasViewedEvent.
 */
class BillInvitationWasViewedEvent extends Event
{
    use SerializesModels;

    public $Bill;
    public $billInvitation;

    /**
     * Create a new event instance.
     *
     * @param Bill $Bill
     * @param BillInvitation $billInvitation
     */
    public function __construct(Bill $Bill, BillInvitation $billInvitation)
    {
        $this->Bill = $Bill;
        $this->billInvitation = $billInvitation;
    }
}
