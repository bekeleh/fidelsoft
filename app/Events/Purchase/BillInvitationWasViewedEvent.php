<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BillInvitationWasViewedEvent.
 */
class BillInvitationWasViewedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $bill;
    public $billInvitation;


    public function __construct($bill, $billInvitation)
    {
        $this->bill = $bill;
        $this->billInvitation = $billInvitation;
    }
}
