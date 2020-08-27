<?php

namespace App\Http\Requests;

class VendorCreditRequest extends EntityRequest
{
    protected $entityType = ENTITY_VENDOR_CREDIT;

    public function authorize()
    {
        return true;
    }

}
