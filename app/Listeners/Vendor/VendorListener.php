<?php

namespace App\Listeners\Vendor;

use App\Events\Vendor\VendorWasCreatedEvent;
use App\Events\Vendor\VendorWasDeletedEvent;
use App\Events\Vendor\VendorWasUpdatedEvent;
use App\Ninja\Transformers\VendorTransformer;
use App\Listeners\EntityListener;

/**
 * Class VendorListener.
 */
class VendorListener extends EntityListener
{

    public function createdVendor(VendorWasCreatedEvent $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);

        $this->checkSubscriptions(EVENT_CREATE_VENDOR, $event->vendor, $transformer, [ENTITY_VENDOR, ENTITY_BILL]);
    }

    public function updatedVendor(VendorWasUpdatedEvent $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);

        $this->checkSubscriptions(EVENT_CREATE_VENDOR, $event->vendor, $transformer, [ENTITY_VENDOR, ENTITY_BILL]);
    }

    public function deletedVendor(VendorWasDeletedEvent $event)
    {
        $transformer = new VendorTransformer($event->vendor->account);

        $this->checkSubscriptions(EVENT_DELETE_VENDOR, $event->vendor, $transformer, [ENTITY_VENDOR, ENTITY_BILL]);
    }
}
