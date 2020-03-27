<?php

namespace App\Http\Requests;

class UpdateExpenseRequest extends ExpenseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
