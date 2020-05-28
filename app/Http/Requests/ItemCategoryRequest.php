<?php

namespace App\Http\Requests;

class ItemCategoryRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_CATEGORY;

    public function authorize()
    {
        return true;
    }
}
