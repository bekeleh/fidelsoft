<?php

namespace App\Http\Requests;

use App\Models\Unit;

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
                $rules['name'] = 'required|max:90|unique:units,name';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $itemCategory = Unit::where('public_id', (int)request()->segment(2))->first();
                if ($itemCategory) {
                    $rules['name'] = 'required|max:90|unique:units,name,' . $itemCategory->id . ',id';
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
}
