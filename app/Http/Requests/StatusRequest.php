<?php

namespace App\Http\Requests;

class StatusRequest extends EntityRequest
{
    protected $entityType = ENTITY_STATUS;

    public function authorize()
    {
        return true;
    }
}
