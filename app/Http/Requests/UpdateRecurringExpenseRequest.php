<?php

namespace App\Http\Requests;

class UpdateRecurringExpenseRequest extends RecurringExpenseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'numeric',
        ];
    }
}
