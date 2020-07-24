<?php

namespace App\Http\Requests;

class VendorContactRequest extends EntityRequest
{
    protected $entityType = ENTITY_VENDOR_CONTACT;

    public function authorize()
    {
        return true;
    }
}
