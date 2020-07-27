<?php

namespace App\Events;

use App\Models\PurchaseCredit;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseCreditWasArchived.
 */
class PurchaseCreditWasArchived extends Event
{
    use SerializesModels;

    public $purchaseCredit;

    /**
     * Create a new event instance.
     *
     * @param PurchaseCredit $purchaseCredit
     */
    public function __construct(PurchaseCredit $purchaseCredit)
    {
        $this->purchaseCredit = $purchaseCredit;
    }
}
