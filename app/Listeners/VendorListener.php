<?php

namespace App\Listeners;

use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasUpdated;
use App\Ninja\Transformers\VendorTransformer;

/**
 * Class VendorListener.
 */
class VendorListener extends EntityListener
{

    public function createdVendor(VendorWasCreated $event)
    {
        $transformer = new VendorTransformer($event->Vendor->account);
        $this->checkSubscriptions(EVENT_CREATE_Vendor, $event->Vendor, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedVendor(VendorWasUpdated $event)
    {
        $transformer = new VendorTransformer($event->Vendor->account);
        $this->checkSubscriptions(EVENT_CREATE_Vendor, $event->Vendor, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedVendor(VendorWasDeleted $event)
    {
        $transformer = new VendorTransformer($event->Vendor->account);
        $this->checkSubscriptions(EVENT_DELETE_Vendor, $event->Vendor, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }
}
