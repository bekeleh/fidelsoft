<?php

namespace App\Events;

use App\Models\BillInvitation;
use App\Models\Bill;
use Illuminate\Queue\SerializesModels;

class BillQuoteInvitationWasApproved extends Event
{
    use SerializesModels;

    public $quote;

    /**
     * @var BillInvitation
     */
    public $billInvitation;

    /**
     * Create a new event instance.
     *
     * @param Bill $quote
     * @param BillInvitation $billInvitation
     */
    public function __construct(Bill $quote, BillInvitation $billInvitation)
    {
        $this->quote = $quote;
        $this->billInvitation = $billInvitation;
    }
}
