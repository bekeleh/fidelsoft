<?php

namespace App\Http\Requests;

class ScheduleRequest extends EntityRequest
{
    protected $entityType = ENTITY_SCHEDULE;

    public function authorize()
    {
        return true;
    }

}
