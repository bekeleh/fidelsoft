<?php

namespace App\Http\Requests;

class ClientTypeRequest extends EntityRequest
{
    protected $entityType = ENTITY_CLIENT_TYPE;

    public function authorize()
    {
        return true;
    }
}
