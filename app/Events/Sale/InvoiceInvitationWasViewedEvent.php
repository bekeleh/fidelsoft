<?php

namespace App\Events\Sale;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceInvitationWasViewedEvent.
 */
class InvoiceInvitationWasViewedEvent extends Event
{
    use SerializesModels, Dispatchable;

    public $invoice;


    public $invitation;

    public function __construct($invoice, $invitation)
    {
        $this->invoice = $invoice;
        $this->invitation = $invitation;
    }
}
