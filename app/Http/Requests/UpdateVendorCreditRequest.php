<?php

namespace App\Http\Requests;

class UpdateVendorCreditRequest extends VendorCreditRequest
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
