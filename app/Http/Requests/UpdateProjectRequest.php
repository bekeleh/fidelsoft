<?php

namespace App\Http\Requests;

class UpdateProjectRequest extends ProjectRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (!$this->entity()) {
            return [];
        }

        return [
            'name' => 'required',
        ];
    }
}
