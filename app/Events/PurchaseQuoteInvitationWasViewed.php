<?php

namespace App\Events;

use App\Models\PurchaseInvitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseQuoteInvitationWasViewed.
 */
class PurchaseQuoteInvitationWasViewed extends Event
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
     * @param $quote
     * @param PurchaseInvitation $invitation
     */
    public function __construct($quote, PurchaseInvitation $invitation)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
