<?php

namespace App\Listeners;

use App\Events\ProductWasCreated;
use App\Events\ProductWasDeleted;
use App\Events\ProductWasUpdated;
use App\Ninja\Transformers\ProductTransformer;

/**
 * Class ProductListener.
 */
class ProductListener extends EntityListener
{

    public function createdProduct(ProductWasCreated $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->product, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedProduct(ProductWasUpdated $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_CREATE_PAYMENT, $event->product, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedProduct(ProductWasDeleted $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_DELETE_PAYMENT, $event->product, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }
}
