<?php

namespace App\Http\Requests;

class CreateBankAccountRequest extends BankAccountRequest
{
    protected $entityType = ENTITY_BANK_ACCOUNT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        return [
            'bank_id' => 'required',
            'bank_username' => 'required',
            'bank_password' => 'required',
        ];
    }
}
