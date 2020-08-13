<?php

namespace App\Http\Requests;

class BillItemRequest extends EntityRequest
{
    protected $entityType = ENTITY_BILL_ITEM;

    public function authorize()
    {
        return true;
    }
}
