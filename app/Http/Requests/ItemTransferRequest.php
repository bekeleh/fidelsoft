<?php

namespace App\Http\Requests;

class ItemTransferRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_TRANSFER;

    public function authorize()
    {
        return true;
    }
}
