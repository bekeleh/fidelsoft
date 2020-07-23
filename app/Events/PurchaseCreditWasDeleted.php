<?php

namespace App\Events;

use App\Models\PurchaseCredit;
use Illuminate\Queue\SerializesModels;

class PurchaseCreditWasDeleted extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseCredit
     */
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
