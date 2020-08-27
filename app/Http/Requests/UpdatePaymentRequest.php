<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends PaymentRequest
{
    protected $entityType = ENTITY_PAYMENT;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }


    public function rules()
    {
        return [];
    }
}
