<?php

namespace App\Http\Requests;

class ItemPriceRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_PRICE;

    public function authorize()
    {
        return true;
    }
}
