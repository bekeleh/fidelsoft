<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\Warehouse;
use App\Models\User;

class CreateBranchRequest extends BranchRequest
{
    protected $entityType = ENTITY_BRANCH;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();
        $rules = [];
        $rules['name'] = 'required|max:90|unique:branches,name,' . $this->id . ',id,account_id,' . $this->account_id;
        $rules['location_id'] = 'required|numeric|exists:locations,id';
        $rules['warehouse_id'] = 'required|numeric|exists:warehouses,id';
        $rules['notes'] = 'nullable';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (!empty($input['warehouse_id'])) {
            $input['warehouse_id'] = filter_var($input['warehouse_id'], FILTER_SANITIZE_NUMBER_INT);
        }
        if (!empty($input['location_id'])) {
            $input['location_id'] = filter_var($input['location_id'], FILTER_SANITIZE_NUMBER_INT);
        }
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

        if (!empty($input['location_id'])) {
            $input['location_id'] = Location::getPrivateId($input['location_id']);
        }
        if (!empty($input['warehouse_id'])) {
            $input['warehouse_id'] = Warehouse::getPrivateId($input['warehouse_id']);
        }
        if (!empty($input['location_id'])) {
            $this->request->add([
                'warehouse_id' => $input['warehouse_id'],
                'location_id' => $input['location_id'],
                'account_id' => User::getAccountId(),
            ]);
        }

        return $this->request->all();
    }
}
