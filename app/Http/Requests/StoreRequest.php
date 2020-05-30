<?php

namespace App\Http\Requests;

class StoreRequest extends EntityRequest
{
    protected $entityType = ENTITY_STORE;

    public function authorize()
    {
        return true;
    }
}
