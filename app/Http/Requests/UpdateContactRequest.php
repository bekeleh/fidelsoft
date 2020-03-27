<?php

namespace App\Http\Requests;

class UpdateContactRequest extends ContactRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
        ];
    }
}
