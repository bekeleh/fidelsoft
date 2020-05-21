<?php

namespace App\Http\Requests;

use App\Models\Department;

class UpdateDepartmentRequest extends EntityRequest
{
    protected $entityType = ENTITY_DEPARTMENT;

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_DEPARTMENT);
    }

    public function rules()
    {
        $rules = [];
        $this->sanitize();
        $this->validationData();
        $department = Department::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
        if ($department)
            $rules['name'] = 'required|max:90|unique:departments,name,' . $department->id . ',id,account_id,' . $department->account_id;
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';
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
                'account_id' => Department::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
