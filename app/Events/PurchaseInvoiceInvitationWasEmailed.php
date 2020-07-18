<?php

namespace App\Events;

use App\Models\PurchaseInvitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseInvitationInvitationWasEmailed.
 */
class PurchaseInvoiceInvitationWasEmailed extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseInvitation
     */
    public $PurchaseInvitation;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvitation $PurchaseInvitation
     */
    public function __construct(PurchaseInvitation $PurchaseInvitation)
    {
        $this->PurchaseInvitation = $PurchaseInvitation;
    }
}
