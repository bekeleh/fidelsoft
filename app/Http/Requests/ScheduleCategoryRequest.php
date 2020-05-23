<?php

namespace App\Http\Requests;

class ScheduleCategoryRequest extends EntityRequest
{
    protected $entityType = ENTITY_SCHEDULE_CATEGORY;

    public function authorize()
    {
        return true;
    }

}
