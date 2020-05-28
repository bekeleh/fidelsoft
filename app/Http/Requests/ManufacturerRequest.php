<?php

namespace App\Http\Requests;

class ManufacturerRequest extends EntityRequest
{
    protected $entityType = ENTITY_MANUFACTURER;

    public function authorize()
    {
        return true;
    }
}
