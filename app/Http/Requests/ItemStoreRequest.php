<?php

namespace App\Http\Requests;

use App\Models\ItemStore;

class ItemStoreRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_STORE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        dd($this->all());
        $this->sanitize();
        $rules = [];
        switch ($this->method()) {
            case 'POST':
            {
                $rules['bin'] = 'required|max:90|unique:item_stores,bin';
//                $rules['product_id'] = 'required|exists:products,id';
//                $rules['store_id'] = 'required|exists:stores,id';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $store = ItemStore::where('public_id', (int)request()->segment(2))->first();
                if ($store) {
                    $rules['bin'] = 'required|max:90|unique:item_stores,bin,' . $store->id . ',id';
//                    $rules['product_id'] = 'required|exists:products,id';
//                $rules['store_id'] = 'required|exists:stores,id';
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
        if (!empty($input['bin'])) {
            $input['bin'] = filter_var($input['bin'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['product_id'])) {
            $input['product_id'] = filter_var($input['product_id'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['store_id'])) {
            $input['store_id'] = filter_var($input['store_id'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
