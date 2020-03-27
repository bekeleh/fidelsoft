<?php

namespace App\Http\Requests;

class ExpenseCategoryRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

}
