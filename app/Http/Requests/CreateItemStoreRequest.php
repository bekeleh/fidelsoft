<?php

namespace App\Http\Requests;

use App\Models\ItemStore;
use App\Models\Product;
use App\Models\Store;

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
        $rules['product_id'] = 'required|unique:item_stores,product_id,' . $this->id . ',id,store_id,' . $this->store_id . ',account_id,' . $this->account_id;
        $rules['store_id'] = 'required|numeric';
        $rules['bin'] = 'required';
        $rules['qty'] = 'numeric';
        $rules['reorder_level'] = 'numeric';
        $rules['EOQ'] = 'numeric';
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
            if (isset($input['store_id'])) {
                $input['store_id'] = filter_var($input['store_id'], FILTER_SANITIZE_NUMBER_INT);
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
        if (isset($input['store_id'])) {
            $input['store_id'] = Store::getPrivateId($input['store_id']);
        }
        if (isset($input['product_id']) && isset($input['store_id'])) {
            $this->request->add([
                'product_id' => $input['product_id'],
                'store_id' => $input['store_id'],
                'account_id' => ItemStore::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
