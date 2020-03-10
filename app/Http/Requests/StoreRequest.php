<?php

namespace App\Http\Requests;

use App\Models\Store;

class StoreRequest extends EntityRequest
{
    protected $entityType = ENTITY_STORE;

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
                $rules['name'] = 'required|max:90|unique:stores,name';
//                $rules['store_code'] = 'required|max:90|unique:stores,store_code';
                $rules['location_id'] = 'required|exists:locations,id';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $store = Store::where('public_id', (int)request()->segment(2))->first();
                dd($store);
                if ($store) {
                    $rules['name'] = 'required|max:90|unique:stores,name,' . $store->id . ',id';
//                    $rules['store_code'] = 'required|max:90|unique:stores,store_code,' . $store->id . ',id';
                    $rules['location_id'] = 'required|exists:locations,id';
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
