<?php

namespace App\Http\Requests;

use App\Models\ApprovalStatus;

class ApprovalStatusRequest extends EntityRequest
{
    protected $entityType = ENTITY_APPROVAL_STATUS;

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
                $rules['name'] = 'required|max:90|unique:approval_statuses,name,' . $this->id . ',id,account_id,' . $this->account_id;
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $approvalStatus = ApprovalStatus::where('public_id', (int)request()->segment(2))->first();
                if ($approvalStatus) {
                    $rules['name'] = 'required|max:90|unique:approval_statuses,name,' . $approvalStatus->id . ',id,account_id,' . $approvalStatus->account_id;
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
                'account_id' => ApprovalStatus::getAccountId(),
            ]);
        }
        return $this->request->all();
    }
}
