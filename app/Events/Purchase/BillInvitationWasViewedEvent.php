<?php

namespace App\Events\Purchase;

use App\Events\Event;
use App\Models\BillInvitation;
use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillInvitationWasViewedEvent.
 */
class BillInvitationWasViewedEvent extends Event
{
    use Dispatchable, Queueable, SerializesModels;

    public $bill;
    public $billInvitation;


    /**
     * BillInvitationWasViewedEvent constructor.
     * @param Bill $bill
     * @param BillInvitation $billInvitation
     */
    public function __construct(Bill $bill, BillInvitation $billInvitation)
    {
        $this->bill = $bill;
        $this->billInvitation = $billInvitation;
    }
}
