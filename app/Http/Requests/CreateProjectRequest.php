<?php

namespace App\Http\Requests;

class CreateProjectRequest extends ProjectRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'client_id' => 'required',
            'task_rate' => 'required',
            'due_date' => 'required',
            'budgeted_hours' => 'required',
        ];
    }
}
