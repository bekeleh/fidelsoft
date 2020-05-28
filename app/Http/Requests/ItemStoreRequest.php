<?php

namespace App\Http\Requests;

class ItemStoreRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_STORE;

    public function authorize()
    {
        return true;
    }
}
