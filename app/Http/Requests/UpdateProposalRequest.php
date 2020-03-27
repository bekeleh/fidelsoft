<?php

namespace App\Http\Requests;

class UpdateProposalRequest extends ProposalRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (!$this->entity()) {
            return [];
        }

        return [
            'invoice_id' => 'required',
        ];
    }
}
