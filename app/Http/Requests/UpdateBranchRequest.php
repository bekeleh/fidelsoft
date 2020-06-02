<?php

namespace App\Http\Requests;

use App\Models\Branch;

class UpdateBranchRequest extends BranchRequest
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
        $branch = $this->entity();
        if ($branch)
            $rules['name'] = 'required|max:90|unique:departments,name,' . $branch->id . ',id,account_id,' . $branch->account_id;
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
                'account_id' => Branch::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
