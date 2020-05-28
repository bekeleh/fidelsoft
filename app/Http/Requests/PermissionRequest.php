<?php

namespace App\Http\Requests;

class PermissionRequest extends EntityRequest
{
    protected $entityType = ENTITY_PERMISSION;

    public function authorize()
    {
        return true;
    }

}
