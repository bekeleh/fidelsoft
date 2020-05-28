<?php

namespace App\Http\Requests;

use App\Models\PermissionGroup;

class UpdatePermissionGroupRequest extends PermissionGroupRequest
{
    protected $entityType = ENTITY_PERMISSION_GROUP;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $group = $this->entity();
        if ($group) {
            $rules['name'] = 'required|max:90|unique:permission_groups,name,' . $group->id . ',id,account_id,' . $group->account_id;
        }
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

    public function validationData()
    {
        $input = $this->input();
        if (count($input)) {
            $this->request->add([
                'account_id' => PermissionGroup::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
