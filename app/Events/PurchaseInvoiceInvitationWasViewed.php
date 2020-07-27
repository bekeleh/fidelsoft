<?php

namespace App\Events;

use App\Models\PurchaseInvitation;
use App\Models\PurchaseInvoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseInvoiceInvitationWasViewed.
 */
class PurchaseInvoiceInvitationWasViewed extends Event
{
    use SerializesModels;

    public $purchaseInvoice;
    public $purchaseInvitation;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvoice $purchaseInvoice
     * @param PurchaseInvitation $purchaseInvitation
     */
    public function __construct(PurchaseInvoice $purchaseInvoice, PurchaseInvitation $purchaseInvitation)
    {
        $this->purchaseInvoice = $purchaseInvoice;
        $this->purchaseInvitation = $purchaseInvitation;
    }
}
