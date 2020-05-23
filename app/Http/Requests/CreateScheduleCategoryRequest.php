<?php

namespace App\Http\Requests;

use App\Models\ExpenseCategory;

class CreateScheduleCategoryRequest extends ScheduleCategoryRequest
{
    protected $entityType = ENTITY_SCHEDULE_CATEGORY;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        $this->validationData();
        $rules['name'] = 'required|unique:schedule_categories,name,' . $this->id . ',id,account_id,' . $this->account_id;

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
