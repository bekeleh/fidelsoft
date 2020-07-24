<?php

namespace App\Http\Requests;

class DeleteVendorContactRequest extends VendorRequest
{
    protected $entityType = ENTITY_VENDOR_CONTACT;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
