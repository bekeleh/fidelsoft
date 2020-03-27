<?php

namespace App\Http\Requests;

class CreateProposalRequest extends ProposalRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'invoice_id' => 'required',
        ];
    }
}
