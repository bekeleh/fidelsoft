<?php

namespace App\Http\Requests;

class CreateTaxRateRequest extends TaxRateRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'rate' => 'required',
        ];
    }
}
