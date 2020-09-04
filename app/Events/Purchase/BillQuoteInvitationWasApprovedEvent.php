<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillQuoteInvitationWasApprovedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $quote;
    public $invitation;


    public function __construct($quote, $invitation)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
