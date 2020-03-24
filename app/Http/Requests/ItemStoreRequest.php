<?php

namespace App\Http\Requests;

use App\Models\ItemStore;
use App\Models\Product;
use App\Models\Store;

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
                $this->validationData();
                $rules['product_id'] = 'required|unique:item_stores,product_id,' . $this->id . ',id,store_id,' . $this->store_id . ',account_id,' . $this->account_id;
                $rules['store_id'] = 'required|numeric';
                $rules['bin'] = 'required';
                $rules['qty'] = 'numeric';
                $rules['reorder_level'] = 'numeric';
                $rules['EOQ'] = 'numeric';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $itemStore = ItemStore::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
                if ($itemStore) {
                    $rules['product_id'] = 'required|unique:item_stores,product_id,' . $itemStore->id . ',id,store_id,' . $itemStore->store_id . ',account_id,' . $itemStore->account_id;
                    $rules['store_id'] = 'required|numeric';
                    $rules['bin'] = 'required';
                    $rules['qty'] = 'numeric';
                    $rules['reorder_level'] = 'numeric';
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
        if (count($input)) {
            if (!empty($input['product_id'])) {
                $input['product_id'] = filter_var($input['product_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['store_id'])) {
                $input['store_id'] = filter_var($input['store_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['qty'])) {
                $input['qty'] = filter_var($input['qty'], FILTER_SANITIZE_NUMBER_FLOAT);
            }
            if (!empty($input['reorder_level'])) {
                $input['reorder_level'] = filter_var($input['reorder_level'], FILTER_SANITIZE_NUMBER_FLOAT);
            }
            if (!empty($input['EOQ'])) {
                $input['EOQ'] = filter_var($input['EOQ'], FILTER_SANITIZE_NUMBER_FLOAT);
            }
            if (!empty($input['bin'])) {
                $input['bin'] = filter_var($input['bin'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['notes'])) {
                $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
            }
            $this->replace($input);
        }
    }

    protected function validationData()
    {
        $input = $this->all();
        if (!empty($input['product_id'])) {
            $input['product_id'] = Product::getPrivateId($input['product_id']);
        }
        if (!empty($input['store_id'])) {
            $input['store_id'] = Store::getPrivateId($input['store_id']);
        }
        if (!empty($input['product_id']) && !empty($input['store_id'])) {
            $this->request->add([
                'product_id' => $input['product_id'],
                'store_id' => $input['store_id'],
                'account_id' => ItemStore::getAccountId()
            ]);
        }
        return $this->request->all();
    }
}
