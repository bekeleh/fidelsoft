<?php

namespace App\Http\Requests;

class CreateBankAccountRequest extends Request
{
    protected $entityType = ENTITY_BANK_ACCOUNT;
    public function authorize()
    {
        return true;
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
