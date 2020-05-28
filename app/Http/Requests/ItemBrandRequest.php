<?php

namespace App\Http\Requests;

class ItemBrandRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_BRAND;

    public function authorize()
    {
        return true;
    }
}
