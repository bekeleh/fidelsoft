<?php

namespace App\Http\Requests;

use App\Models\Location;

class UpdateLocationRequest extends LocationRequest
{
    protected $entityType = ENTITY_LOCATION;

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_LOCATION);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $location = $this->entity();
        if ($location)
            $rules['name'] = 'required|max:90|unique:locations,name,' . $location->id . ',id,account_id,' . $location->account_id;
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
                'account_id' => Location::getAccountId()
            ]);
        }
        return $this->request->all();
    }
}
