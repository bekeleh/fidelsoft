<?php

namespace App\Http\Requests;

use App\Models\Unit;

class UpdateUnitRequest extends UnitRequest
{
    protected $entityType = ENTITY_UNIT;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $unit = $this->entity();
        if ($unit) {
            $rules['name'] = 'required|max:90|unique:units,name,' . $unit->id . ',id,account_id,' . $unit->account_id;
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
                'account_id' => Unit::getAccountId()
            ]);
        }
        return $this->request->all();
    }
}
