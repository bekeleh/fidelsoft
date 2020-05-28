<?php

namespace App\Http\Requests;

class PermissionGroupRequest extends EntityRequest
{
    protected $entityType = ENTITY_PERMISSION_GROUP;

    public function authorize()
    {
        return true;
    }
}
