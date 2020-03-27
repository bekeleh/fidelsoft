<?php

namespace App\Http\Requests;

class ProposalCategoryRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
