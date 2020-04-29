<?php

namespace App\Http\Requests;

class UpdateTaxRateRequest extends TaxRateRequest
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
