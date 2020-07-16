<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\Warehouse;

class UpdateWarehouseRequest extends WarehouseRequest
{
    protected $entityType = ENTITY_WAREHOUSE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $warehouse = $this->entity();
        if ($warehouse) {
            $rules['name'] = 'required|max:90|unique:warehouses,name,' . $warehouse->id . ',id,account_id,' . $warehouse->account_id;
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
                    'account_id' => Warehouse::getAccountId()
                ]);
            }
        }

        return $this->request->all();
    }
}
