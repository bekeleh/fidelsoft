<?php

namespace App\Http\Requests;

class UpdateTaskRequest extends TaskRequest
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
