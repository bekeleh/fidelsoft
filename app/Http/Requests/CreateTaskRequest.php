<?php

namespace App\Http\Requests;

class CreateTaskRequest extends TaskRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'time_log' => 'time_log',
        ];
    }
}
