<?php

namespace App\Http\Requests;

class UpdateDocumentRequest extends DocumentRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

        ];
    }
}
