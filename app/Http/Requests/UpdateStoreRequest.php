<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\Store;

class UpdateStoreRequest extends StoreRequest
{
    protected $entityType = ENTITY_STORE;

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_STORE);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $store = $this->entity();
        if ($store) {
            $rules['name'] = 'required|max:90|unique:stores,name,' . $store->id . ',id,account_id,' . $store->account_id;
            $rules['store_code'] = 'required|max:90|unique:stores,store_code,' . $store->id . ',id';
        }
        $rules['location_id'] = 'numeric';
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
        if (!empty($input['location_id'])) {
            $input['location_id'] = filter_var($input['location_id'], FILTER_SANITIZE_STRING);
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
            if (!empty($input['location_id'])) {
                $input['location_id'] = Location::getPrivateId($input['location_id']);
            }
            if (!empty($input['location_id'])) {
                $this->request->add([
                    'location_id' => $input['location_id'],
                    'account_id' => Store::getAccountId()
                ]);
            }
        }

        return $this->request->all();
    }
}
