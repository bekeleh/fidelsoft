<?php

namespace App\Http\Requests;

class UpdateCreditRequest extends CreditRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'positive',
        ];
    }
}
