<?php

namespace App\Http\Requests;

class CreateProposalCategoryRequest extends ProposalCategoryRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => sprintf('required|unique:proposal_categories,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
