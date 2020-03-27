<?php

namespace App\Http\Requests;

class CreateBankAccountRequest extends Request
{

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
