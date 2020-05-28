<?php

namespace App\Http\Requests;

use App\Models\HoldReason;

class UpdateHoldReasonRequest extends HoldReasonRequest
{
    protected $entityType = ENTITY_HOLD_REASON;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $holdReason = HoldReason::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
        if ($holdReason) {
            $rules['name'] = 'required|max:90|unique:hold_reasons,name,' . $holdReason->id . ',id,account_id,' . $holdReason->account_id;
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
