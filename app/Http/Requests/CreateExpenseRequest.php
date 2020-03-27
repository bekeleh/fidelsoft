<?php

namespace App\Http\Requests;

class CreateExpenseRequest extends ExpenseRequest
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
