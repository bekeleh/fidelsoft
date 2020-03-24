<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class UnitRequest extends EntityRequest
{
    protected $entityType = ENTITY_UNIT;

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
                $this->validationData();
                $rules['name'] = 'required|max:90|unique:units,name,' . $this->id . ',id,account_id,' . $this->account_id;
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $itemCategory = Unit::where('public_id', (int)request()->segment(2))->first();
                if ($itemCategory) {
                    $rules['name'] = 'required|max:90|unique:units,name,' . $itemCategory->id . ',id,account_id,' . $itemCategory->account_id;
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
            $unit = Unit::createNew();
            if (!empty($unit)) {
                $input['account_id'] = $unit->account_id;
            }
            if (!empty($input['account_id'])) {
                $this->request->add([
                    'account_id' => $input['account_id']
                ]);
            }
        }
        return $this->request->all();
    }
}
