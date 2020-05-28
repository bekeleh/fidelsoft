<?php

namespace App\Http\Requests;

use App\Models\ItemTransfer;
use App\Models\Store;

class UpdateItemTransferRequest extends ItemTransferRequest
{
    protected $entityType = ENTITY_ITEM_TRANSFER;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();
        $rules = [];
        $itemTransfer = $this->entity();
        $rules['product_id'] = 'required|array';
        $rules['previous_store_id'] = 'required|numeric';
        $rules['current_store_id'] = 'required|numeric|different:previous_store_id';
//                    $rules['approver_id'] = 'required|numeric|exists:statuses';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (count($input)) {
            if (!empty($input['current_store_id'])) {
                $input['current_store_id'] = filter_var($input['current_store_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['previous_store_id'])) {
                $input['previous_store_id'] = filter_var($input['previous_store_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['approver_id'])) {
                $input['approver_id'] = filter_var($input['approver_id'], FILTER_SANITIZE_NUMBER_INT);
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

        if (!empty($input['previous_store_id'])) {
            $input['previous_store_id'] = Store::getPrivateId($input['previous_store_id']);
        }
        if (!empty($input['current_store_id'])) {
            $input['current_store_id'] = Store::getPrivateId($input['current_store_id']);
        }
        if (!empty($input['previous_store_id']) && !empty($input['current_store_id'])) {
            $this->request->add([
                'previous_store_id' => $input['previous_store_id'],
                'current_store_id' => $input['current_store_id'],
                'account_id' => ItemTransfer::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
