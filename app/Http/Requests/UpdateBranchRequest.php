<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\Store;
use App\Models\User;

class UpdateBranchRequest extends BranchRequest
{
    protected $entityType = ENTITY_BRANCH;

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_BRANCH);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $branch = $this->entity();
        if ($branch)
            $rules['name'] = 'required|max:90|unique:branches,name,' . $branch->id . ',id,account_id,' . $branch->account_id;
        $rules['store_id'] = 'required|numeric|exists:stores,id';
        $rules['location_id'] = 'required|numeric|exists:locations,id';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        if (!empty($input['store_id'])) {
            $input['store_id'] = filter_var($input['store_id'], FILTER_SANITIZE_NUMBER_INT);
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
        if (!empty($input['store_id'])) {
            $input['store_id'] = Store::getPrivateId($input['store_id']);
        }
        if (!empty($input['location_id'])) {
            $this->request->add([
                'store_id' => $input['store_id'],
                'location_id' => $input['location_id'],
                'account_id' => User::getAccountId(),
            ]);
        }

        return $this->request->all();
    }
}
