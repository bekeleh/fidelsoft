<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class CreateLocationRequest extends EntityRequest
{
    protected $entityType = ENTITY_LOCATION;

    public function authorize()
    {
        return Auth::user()->can('create', ENTITY_LOCATION);
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        $this->validationData();
        $rules['name'] = 'required|max:90|unique:locations,name,' . $this->id . ',id,account_id,' . $this->account_id;
        $rules['notes'] = 'nullable';
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
