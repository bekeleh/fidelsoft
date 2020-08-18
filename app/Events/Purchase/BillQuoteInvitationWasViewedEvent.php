<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\Bill;
use App\Models\BillInvitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillQuoteInvitationWasViewedEvent.
 */
class BillQuoteInvitationWasViewedEvent extends Event
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
     * @param $quote
     * @param BillInvitation $billInvitation
     */
    public function __construct(Bill $quote, BillInvitation $billInvitation)
    {
        $this->quote = $quote;
        $this->billInvitation = $billInvitation;
    }
}
