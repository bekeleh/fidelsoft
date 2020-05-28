<?php

namespace App\Http\Requests;

class UserRequest extends EntityRequest
{
    protected $entityType = ENTITY_USER;

    public function authorize()
    {
        return true;
    }
}
