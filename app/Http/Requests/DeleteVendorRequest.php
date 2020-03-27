<?php

namespace App\Http\Requests;

class DeleteVendorRequest extends VendorRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
