<?php

namespace App\Http\Requests;

class PointOfSaleRequest extends EntityRequest
{
    protected $entityType = ENTITY_PRODUCT;

    public function authorize()
    {
        return true;
    }
}
