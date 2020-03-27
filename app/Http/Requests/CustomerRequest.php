<?php

namespace App\Http\Requests;

class CustomerRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
