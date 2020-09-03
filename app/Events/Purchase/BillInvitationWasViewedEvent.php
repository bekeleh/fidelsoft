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
    public $invitation;


    public function __construct($bill, $invitation)
    {
        $this->bill = $bill;
        $this->invitation = $invitation;
    }
}
