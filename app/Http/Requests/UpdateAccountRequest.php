<?php

namespace App\Http\Requests;

class UpdateAccountRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'logo' => 'sometimes|max:' . MAX_LOGO_FILE_SIZE . '|mimes:jpeg,gif,png',
        ];
    }
}
//
