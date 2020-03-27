<?php

namespace App\Http\Requests;

class CreateCreditRequest extends CreditRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => 'required',
            'amount' => 'required|positive',
        ];
    }
}
