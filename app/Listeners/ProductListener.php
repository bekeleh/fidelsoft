<?php

namespace App\Listeners;

use App\Events\ProductWasCreatedEvent;
use App\Events\ProductWasDeletedEvent;
use App\Events\ProductWasUpdatedEvent;
use App\Ninja\Transformers\ProductTransformer;
use App\Listeners\Common\EntityListener;
/**
 * Class ProductListener.
 */
class ProductListener extends EntityListener
{

    public function createdProduct(ProductWasCreatedEvent $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_CREATE_PRODUCT, $event->product, $transformer, [ENTITY_PRODUCT]);
    }

    public function updatedProduct(ProductWasUpdatedEvent $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_UPDATE_PRODUCT, $event->product, $transformer, [ENTITY_PRODUCT]);
    }

    public function deletedProduct(ProductWasDeletedEvent $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_DELETE_PRODUCT, $event->product, $transformer, [ENTITY_PRODUCT]);
    }
}
