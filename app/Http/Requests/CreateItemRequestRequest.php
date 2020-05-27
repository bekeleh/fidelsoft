<?php

namespace App\Http\Requests;

use App\Models\Department;
use App\Models\ItemRequest;
use App\Models\Product;
use App\Models\Store;

class CreateItemRequestRequest extends ItemRequestRequest
{
    protected $entityType = ENTITY_ITEM_REQUEST;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $rules['product_id'] = 'required|numeric';
        $rules['department_id'] = 'required|numeric';
        $rules['store_id'] = 'required|numeric';
//        $rules['status_id'] = 'required|numeric|exists:statuses,id';
//        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (count($input)) {
            if (!empty($input['product_id'])) {
                $input['product_id'] = filter_var($input['product_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['department_id'])) {
                $input['department_id'] = filter_var($input['department_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['store_id'])) {
                $input['store_id'] = filter_var($input['store_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['status_id'])) {
                $input['status_id'] = filter_var($input['status_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['qty'])) {
                $input['qty'] = filter_var($input['qty'], FILTER_SANITIZE_NUMBER_INT);
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
        if (!empty($input['department_id'])) {
            $input['department_id'] = Department::getPrivateId($input['department_id']);
        }
        if (!empty($input['store_id'])) {
            $input['store_id'] = Store::getPrivateId($input['store_id']);
        }
//        if (!empty($input['status_id'])) {
//            $input['status_id'] = Store::getPrivateId($input['status_id']);
//        }
        if (!empty($input['product_id']) && !empty($input['department_id']) && !empty($input['store_id'])) {
            $this->request->add([
                'product_id' => $input['product_id'],
                'store_id' => $input['store_id'],
                'department_id' => $input['department_id'],
//                'status_id' => $input['status_id'],
                'account_id' => ItemRequest::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
