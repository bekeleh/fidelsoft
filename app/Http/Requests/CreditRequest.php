<?php

namespace App\Http\Requests;

class CreditRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
