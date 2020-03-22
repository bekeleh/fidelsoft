<?php

namespace App\Http\Requests;

use App\Models\HoldReason;

class HoldReasonRequest extends EntityRequest
{
    protected $entityType = ENTITY_HOLD_REASON;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        switch ($this->method()) {
            case 'POST':
            {
                $rules['reason'] = 'required|max:90|unique:hold_reasons,reason';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $holdReason = HoldReason::where('public_id', (int)request()->segment(2))->first();
                if ($holdReason) {
                    $rules['reason'] = 'required|max:90|unique:hold_reasons,reason,' . $holdReason->id . ',id';
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
        if (!empty($input['reason'])) {
            $input['reason'] = filter_var($input['reason'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
