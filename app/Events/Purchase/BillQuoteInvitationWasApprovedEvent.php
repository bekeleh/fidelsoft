<?php

namespace App\Events\Purchase;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillQuoteInvitationWasApprovedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $quote;
    public $billInvitation;


    public function __construct($quote, $billInvitation)
    {
        $this->quote = $quote;
        $this->billInvitation = $billInvitation;
    }
}
