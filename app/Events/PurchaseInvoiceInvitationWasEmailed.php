<?php

namespace App\Events;

use App\Models\PurchaseInvitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseInvoiceInvitationWasEmailed.
 */
class PurchaseInvoiceInvitationWasEmailed extends Event
{
    use SerializesModels;

    public $purchaseInvitation;
    public $notes;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvitation $purchaseInvitation
     * @param mixed $notes
     */
    public function __construct(PurchaseInvitation $purchaseInvitation, $notes)
    {
        $this->purchaseInvitation = $purchaseInvitation;
        $this->notes = $notes;
    }
}
