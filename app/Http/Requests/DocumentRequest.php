<?php

namespace App\Http\Requests;

class DocumentRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
