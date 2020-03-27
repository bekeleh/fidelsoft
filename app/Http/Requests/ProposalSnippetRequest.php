<?php

namespace App\Http\Requests;

use App\Models\ProposalCategory;

class ProposalSnippetRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
