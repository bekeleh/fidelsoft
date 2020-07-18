<?php

namespace App\Events;

use App\Models\PurchaseItem;
use Illuminate\Queue\SerializesModels;

/**
 * Class PurchaseItemWasCreated.
 */
class PurchaseItemWasUpdated extends Event
{
    use SerializesModels;

    /**
     * @var PurchaseItem
     */
    public $PurchaseItem;

    /**
     * Create a new event instance.
     *
     * @param PurchaseItem $PurchaseItem
     */
    public function __construct(PurchaseItem $PurchaseItem)
    {
        $this->PurchaseItem = $PurchaseItem;
    }
}
