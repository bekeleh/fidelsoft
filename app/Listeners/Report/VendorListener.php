<?php

namespace App\Listeners\Report;

use App\Events\VendorWasCreatedEvent;
use App\Events\VendorWasDeletedEvent;
use App\Events\VendorWasUpdatedEvent;
use App\Ninja\Transformers\VendorTransformer;
use App\Listeners\Common\EntityListener;

/**
 * Class VendorListener.
 */
class VendorListener extends EntityListener
{

    public function createdVendor(VendorWasCreatedEvent $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);
        $this->checkSubscriptions(EVENT_CREATE_VENDOR, $event->vendor, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedVendor(VendorWasUpdatedEvent $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);
        $this->checkSubscriptions(EVENT_CREATE_VENDOR, $event->vendor, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedVendor(VendorWasDeletedEvent $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);
        $this->checkSubscriptions(EVENT_DELETE_VENDOR, $event->vendor, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }
}
