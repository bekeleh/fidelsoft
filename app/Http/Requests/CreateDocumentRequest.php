<?php

namespace App\Http\Requests;

class CreateDocumentRequest extends DocumentRequest
{
    protected $autoload = [
        ENTITY_INVOICE,
        ENTITY_EXPENSE,
    ];

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //'file' => 'mimes:jpg'
        ];
    }
}
