<?php

namespace App\Http\Requests;

class DeleteVendorRequest extends VendorRequest
{
    protected $entityType = ENTITY_VENDOR;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
