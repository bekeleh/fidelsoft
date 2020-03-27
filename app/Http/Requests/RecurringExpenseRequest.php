<?php

namespace App\Http\Requests;

class RecurringExpenseRequest extends ExpenseRequest
{
    public function authorize()
    {
        return true;
    }

}
