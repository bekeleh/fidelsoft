<?php

namespace App\Http\Requests;

class ProductRequest extends EntityRequest
{
    protected $entityType = ENTITY_PRODUCT;

    public function authorize()
    {
        return true;
    }
}
