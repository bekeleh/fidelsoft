<?php

namespace App\Events;

use App\Models\PurchaseInvitation;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseInvitationInvitationWasEmailed.
 */
class PurchaseInvitationWasEmailed extends Event
{
    use SerializesModels;

    public $PurchaseInvitation;

    public $notes;

    /**
     * Create a new event instance.
     *
     * @param PurchaseInvitation $PurchaseInvitation
     * @param $notes
     */
    public function __construct(PurchaseInvitation $PurchaseInvitation, $notes)
    {
        $this->PurchaseInvitation = $PurchaseInvitation;
        $this->notes = $notes;
    }
}
