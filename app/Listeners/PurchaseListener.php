<?php

namespace App\Listeners;

use App\Events\PurchaseWasCreated;
use App\Events\PurchaseWasDeleted;
use App\Events\PurchaseWasUpdated;
use App\Ninja\Transformers\PurchaseTransformer;

/**
 * Class PurchaseListener.
 */
class PurchaseListener extends EntityListener
{

    public function createdPurchase(PurchaseWasCreated $event)
    {
        $transformer = new PurchaseTransformer($event->Purchase->account);
        $this->checkSubscriptions(EVENT_CREATE_Purchase, $event->Purchase, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedPurchase(PurchaseWasUpdated $event)
    {
        $transformer = new PurchaseTransformer($event->Purchase->account);
        $this->checkSubscriptions(EVENT_CREATE_Purchase, $event->Purchase, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedPurchase(PurchaseWasDeleted $event)
    {
        $transformer = new PurchaseTransformer($event->Purchase->account);
        $this->checkSubscriptions(EVENT_DELETE_Purchase, $event->Purchase, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }
}
