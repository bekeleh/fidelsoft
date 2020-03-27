<?php

namespace App\Http\Requests;

class PaymentRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
