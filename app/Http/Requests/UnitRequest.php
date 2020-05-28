<?php

namespace App\Http\Requests;

class UnitRequest extends EntityRequest
{
    protected $entityType = ENTITY_UNIT;

    public function authorize()
    {
        return true;
    }

}
