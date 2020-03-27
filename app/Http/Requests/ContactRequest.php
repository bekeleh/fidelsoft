<?php

namespace App\Http\Requests;

class ContactRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }
}
