<?php

namespace App\Http\Requests;

class DocumentRequest extends EntityRequest
{
    protected $entityType = ENTITY_DOCUMENT;

    public function authorize()
    {
        return true;
    }

}
