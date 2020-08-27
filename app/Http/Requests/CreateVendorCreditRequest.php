<?php

namespace App\Http\Requests;

class CreateVendorCreditRequest extends VendorCreditRequest
{
    protected $entityType = ENTITY_VENDOR_CREDIT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        return [
            'vendor_id' => 'required',
            'amount' => 'required|positive',
        ];
    }
}
