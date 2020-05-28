<?php

namespace App\Http\Requests;

class ItemMovementRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_MOVEMENT;

    public function authorize()
    {
        return true;
    }

}
