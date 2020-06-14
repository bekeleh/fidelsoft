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
        $this->checkSubscriptions(EVENT_CREATE_PRODUCT, $event->product, $transformer, [ENTITY_PRODUCT]);
    }

    public function updatedProduct(ProductWasUpdated $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_UPDATE_PRODUCT, $event->product, $transformer, [ENTITY_PRODUCT]);
    }

    public function deletedProduct(ProductWasDeleted $event)
    {
        $transformer = new ProductTransformer($event->product->account);
        $this->checkSubscriptions(EVENT_DELETE_PRODUCT, $event->product, $transformer, [ENTITY_PRODUCT]);
    }
}
