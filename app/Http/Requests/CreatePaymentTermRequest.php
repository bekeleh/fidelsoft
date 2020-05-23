<?php

namespace App\Http\Requests;

class CreatePaymentTermRequest extends PaymentTermRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {

        $rules = [
            'num_days' => 'required|numeric|unique:payment_terms,num_days,,id,account_id,' . $this->user()->account_id . ',deleted_at,NULL'
                . '|unique:payment_terms,num_days,,id,account_id,0,deleted_at,NULL',
        ];


        return $rules;
    }
}
