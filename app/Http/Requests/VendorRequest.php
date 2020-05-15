<?php

namespace App\Http\Requests;

class VendorRequest extends EntityRequest
{
    protected $entityType = ENTITY_VENDOR;

    public function entity()
    {
        $vendor = parent::entity();

        // eager load the contacts
        if ($vendor && method_exists($vendor, 'vendor_contacts') && !$vendor->relationLoaded('vendor_contacts')) {
            $vendor->load('vendor_contacts');
        }

        return $vendor;
    }
}
