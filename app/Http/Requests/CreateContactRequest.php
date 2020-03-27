<?php

namespace App\Http\Requests;

class CreateContactRequest extends ContactRequest
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
            'client_id' => 'required',
        ];
    }
}
