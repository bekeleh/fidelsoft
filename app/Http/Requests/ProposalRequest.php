<?php

namespace App\Http\Requests;

class ProposalRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
