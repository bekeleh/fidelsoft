<?php

namespace App\Http\Requests;

use App\Models\ItemStore;
use App\Models\Product;
use App\Models\Warehouse;

class CreateItemStoreRequest extends ItemStoreRequest
{
    protected $entityType = ENTITY_ITEM_STORE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $rules['product_id'] = 'required|unique:item_stores,product_id,' . $this->id . ',id,warehouse_id,' . $this->warehouse_id . ',account_id,' . $this->account_id;
        $rules['warehouse_id'] = 'required|numeric:exists,warehouses';
        $rules['bin'] = 'required';
        $rules['new_qty'] = 'numeric|required';
        $rules['reorder_level'] = 'numeric';
        $rules['EOQ'] = 'nullable';
        $rules['notes'] = 'nullable';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (isset($input)) {
            if (isset($input['product_id'])) {
                $input['product_id'] = filter_var($input['product_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (isset($input['warehouse_id'])) {
                $input['warehouse_id'] = filter_var($input['warehouse_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (isset($input['qty'])) {
                $input['qty'] = filter_var($input['qty'], FILTER_SANITIZE_NUMBER_FLOAT);
            }
            if (isset($input['reorder_level'])) {
                $input['reorder_level'] = filter_var($input['reorder_level'], FILTER_SANITIZE_NUMBER_FLOAT);
            }
            if (isset($input['EOQ'])) {
                $input['EOQ'] = filter_var($input['EOQ'], FILTER_SANITIZE_NUMBER_FLOAT);
            }
            if (isset($input['bin'])) {
                $input['bin'] = filter_var($input['bin'], FILTER_SANITIZE_STRING);
            }
            if (isset($input['notes'])) {
                $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
            }

            $this->replace($input);
        }
    }

    protected function validationData()
    {
        $input = $this->all();
        if (isset($input['product_id'])) {
            $input['product_id'] = Product::getPrivateId($input['product_id']);
        }
        if (isset($input['warehouse_id'])) {
            $input['warehouse_id'] = Warehouse::getPrivateId($input['warehouse_id']);
        }
        if (isset($input['product_id']) && isset($input['warehouse_id'])) {
            $this->request->add([
                'product_id' => $input['product_id'],
                'warehouse_id' => $input['warehouse_id'],
                'account_id' => ItemStore::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
