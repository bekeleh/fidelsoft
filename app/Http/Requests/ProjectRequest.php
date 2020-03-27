<?php

namespace App\Http\Requests;

class ProjectRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
