<?php

namespace App\Http\Requests;

class ItemStoreRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_STORE;

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
                $rules['product_id'] = 'required|numeric';
                $rules['store_id'] = 'required|numeric';
                $rules['bin'] = 'required';
                $rules['qty'] = 'numeric';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules['product_id'] = 'required|numeric';
                $rules['store_id'] = 'required|numeric';
                $rules['bin'] = 'required';
                $rules['qty'] = 'numeric';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            default:
                break;
        }
        return $rules;
    }

    public
    function sanitize()
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