<?php

namespace App\Http\Requests;

use App\Models\SaleType;

class SaleTypeRequest extends EntityRequest
{
    protected $entityType = ENTITY_SALE_TYPE;

    public function authorize()
    {
        return true;
    }
}
