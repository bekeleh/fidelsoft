<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\Store;

class StoreRequest extends EntityRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        switch ($this->method()) {
            case 'POST':
            {
                $this->validationData();
                $rules['name'] = 'required|max:90|unique:stores,name,' . $this->id . ',id,account_id,' . $this->account_id;
                $rules['store_code'] = 'required|max:90|unique:stores,store_code';
                $rules['location_id'] = 'numeric';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $store = Store::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
                if ($store) {
                    $rules['name'] = 'required|max:90|unique:stores,name,' . $store->id . ',id,account_id,' . $store->account_id;
                    $rules['store_code'] = 'required|max:90|unique:stores,store_code,' . $store->id . ',id';
                    $rules['location_id'] = 'numeric';
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
