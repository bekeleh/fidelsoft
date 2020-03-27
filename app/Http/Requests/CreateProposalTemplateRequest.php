<?php

namespace App\Http\Requests;

class CreateProposalTemplateRequest extends ProposalTemplateRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => sprintf('required|unique:proposal_templates,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
