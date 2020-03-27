<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends PaymentRequest
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
