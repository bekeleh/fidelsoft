<?php

namespace App\Http\Requests;

class WarehouseRequest extends EntityRequest
{
    protected $entityType = ENTITY_WAREHOUSE;

    public function authorize()
    {
        return true;
    }
}
