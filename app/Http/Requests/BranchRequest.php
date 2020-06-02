<?php

namespace App\Http\Requests;

class BranchRequest extends EntityRequest
{
    protected $entityType = ENTITY_BRANCH;

    public function authorize()
    {
        return true;
    }
}
