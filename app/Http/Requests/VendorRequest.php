<?php

namespace App\Http\Requests;

class VendorRequest extends EntityRequest
{
    protected $entityType = ENTITY_VENDOR;

    public function entity()
    {
        $vendor = parent::entity();

        // eager load the contacts
        if ($vendor && method_exists($vendor, 'contacts') && !$vendor->relationLoaded('contacts')) {
            $vendor->load('contacts');
        }

        return $vendor;
    }
}
