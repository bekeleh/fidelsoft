<?php

namespace App\Http\Requests;

use App\Models\ExpenseCategory;

class UpdateExpenseCategoryRequest extends ExpenseCategoryRequest
{
    protected $entityType = ENTITY_EXPENSE_CATEGORY;

    public function authorize()
    {
        return $this->user()->can('edit', ENTITY_EXPENSE_CATEGORY);
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        $this->validationData();
        $expenseCategory = ExpenseCategory::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
        if ($expenseCategory)
            $rules['name'] = 'required|unique:expense_categories,name,' . $expenseCategory->id . ',id,account_id,' . $expenseCategory->account_id;

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        if (!empty($input['name'])) {
            $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    protected function validationData()
    {
        $input = $this->all();

        if (count($input)) {
            $this->request->add([
                'account_id' => ExpenseCategory::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
