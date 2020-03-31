<?php

namespace App\Http\Requests;

class ProposalCategoryRequest extends EntityRequest
{
    protected $entityType = ENTITY_PROPOSAL_CATEGORY;

    public function authorize()
    {
        return true;
    }

}
