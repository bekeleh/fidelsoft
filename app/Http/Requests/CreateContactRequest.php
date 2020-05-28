<?php

namespace App\Http\Requests;

class CreateContactRequest extends ContactRequest
{
    protected $entityType = ENTITY_CONTACT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'client_id' => 'required',
        ];
    }
}
