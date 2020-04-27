<?php

namespace App\Http\Requests;

use App\Models\PermissionGroup;

class PermissionGroupRequest extends EntityRequest
{
    protected $entityType = ENTITY_PERMISSION_GROUP;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        switch ($this->method()) {
            case 'POST':
            {
                $this->validationData();
                $rules['name'] = 'required|max:90|unique:permission_groups,name,' . $this->id . ',id,account_id,' . $this->account_id;
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $group = PermissionGroup::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
                if ($group) {
                    $rules['name'] = 'required|max:90|unique:permission_groups,name,' . $group->id . ',id,account_id,' . $group->account_id;
                    $rules['is_deleted'] = 'boolean';
                    $rules['notes'] = 'nullable';
                    break;
                } else {
                    return;
                }
            }
            default:
                break;
        }
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
