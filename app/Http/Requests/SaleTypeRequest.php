<?php

namespace App\Http\Requests;

class SaleTypeRequest extends EntityRequest
{
    protected $entityType = ENTITY_SALE_TYPE;

    public function authorize()
    {
        return true;
    }
}
