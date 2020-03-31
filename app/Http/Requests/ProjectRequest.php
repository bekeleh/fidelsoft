<?php

namespace App\Http\Requests;

class ProjectRequest extends EntityRequest
{
    protected $entityType = ENTITY_PROJECT;

    public function authorize()
    {
        return true;
    }

}
