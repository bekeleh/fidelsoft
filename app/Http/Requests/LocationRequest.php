<?php

namespace App\Http\Requests;

class LocationRequest extends EntityRequest
{
    protected $entityType = ENTITY_LOCATION;

    public function authorize()
    {
        return true;
    }
}
