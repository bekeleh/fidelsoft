<?php

namespace App\Http\Requests;

use App\Models\Unit;

class CreateUnitRequest extends UnitRequest
{
    protected $entityType = ENTITY_UNIT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $rules['name'] = 'required|max:90|unique:units,name,' . $this->id . ',id,account_id,' . $this->account_id;
        $rules['notes'] = 'nullable';
        $rules['is_deleted'] = 'boolean';

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
                'account_id' => Unit::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
