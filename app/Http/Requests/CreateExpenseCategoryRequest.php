<?php

namespace App\Http\Requests;

class CreateExpenseCategoryRequest extends ExpenseCategoryRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => sprintf('required|unique:expense_categories,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
