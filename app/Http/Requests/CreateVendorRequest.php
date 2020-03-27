<?php

namespace App\Http\Requests;

class CreateVendorRequest extends VendorRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
