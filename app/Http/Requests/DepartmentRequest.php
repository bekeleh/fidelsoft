<?php

namespace App\Http\Requests;

class DepartmentRequest extends EntityRequest
{
    protected $entityType = ENTITY_DEPARTMENT;

    public function authorize()
    {
        return true;
    }
}
