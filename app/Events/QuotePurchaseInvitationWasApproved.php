<?php

namespace App\Events;

use App\Models\PurchaseInvitation;
use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

class QuotePurchaseInvitationWasApproved extends Event
{
    use SerializesModels;

    public $quote;

    /**
     * @var PurchaseInvitation
     */
    public $invitation;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvoice $quote
     * @param PurchaseInvitation $invitation
     */
    public function __construct(PurchaseInvoice $quote, PurchaseInvitation $invitation)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
