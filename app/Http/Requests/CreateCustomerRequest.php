<?php

namespace App\Http\Requests;

class CreateCustomerRequest extends CustomerRequest
{
    protected $entityType = ENTITY_CUSTOMER;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $rules = [
            'token' => 'required',
            'client_id' => 'required',
            'contact_id' => 'required',
            'payment_method.source_reference' => 'required',
        ];

        return $rules;
    }
}
