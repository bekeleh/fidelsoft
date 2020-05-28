<?php

namespace App\Http\Requests;

class CreateCreditRequest extends CreditRequest
{
    protected $entityType = ENTITY_CREDIT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        return [
            'client_id' => 'required',
            'amount' => 'required|positive',
        ];
    }
}
