<?php

namespace App\Listeners\Setting;

use App\Events\Setting\ProductWasCreatedEvent;
use App\Events\Setting\ProductWasDeletedEvent;
use App\Events\Setting\ProductWasUpdatedEvent;
use App\Ninja\Transformers\ProductTransformer;
use App\Listeners\EntityListener;

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
