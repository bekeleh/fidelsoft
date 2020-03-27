<?php

namespace App\Http\Requests;

use App\Models\HoldReason;

class HoldReasonRequest extends EntityRequest
{
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
                $rules['name'] = 'required|max:90|unique:hold_reasons,name,' . $this->id . ',id,account_id,' . $this->account_id;
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $holdReason = HoldReason::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
                if ($holdReason) {
                    $rules['name'] = 'required|max:90|unique:hold_reasons,name,' . $holdReason->id . ',id,account_id,' . $holdReason->account_id;
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

    protected function validationData()
    {
        $input = $this->all();

        if (count($input)) {
            $this->request->add([
                'account_id' => HoldReason::getAccountId()
            ]);
        }
        return $this->request->all();
    }
}
