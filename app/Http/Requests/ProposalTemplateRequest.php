<?php

namespace App\Http\Requests;

class ProposalTemplateRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
