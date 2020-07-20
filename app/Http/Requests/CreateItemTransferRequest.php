<?php

namespace App\Http\Requests;

use App\Models\ItemTransfer;
use App\Models\Warehouse;

class CreateItemTransferRequest extends ItemTransferRequest
{
    protected $entityType = ENTITY_ITEM_TRANSFER;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $rules['product_id'] = 'required|array';
        $rules['previous_warehouse_id'] = 'required|numeric';
        $rules['current_warehouse_id'] = 'required|numeric|different:previous_warehouse_id';
//                $rules['approver_id'] = 'required|numeric|exists:statuses';
        $rules['notes'] = 'nullable';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';
        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (count($input)) {
            if (!empty($input['current_warehouse_id'])) {
                $input['current_warehouse_id'] = filter_var($input['current_warehouse_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['previous_warehouse_id'])) {
                $input['previous_warehouse_id'] = filter_var($input['previous_warehouse_id'], FILTER_SANITIZE_NUMBER_INT);
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

        if (!empty($input['previous_warehouse_id'])) {
            $input['previous_warehouse_id'] = Warehouse::getPrivateId($input['previous_warehouse_id']);
        }
        if (!empty($input['current_warehouse_id'])) {
            $input['current_warehouse_id'] = Warehouse::getPrivateId($input['current_warehouse_id']);
        }
        if (!empty($input['previous_warehouse_id']) && !empty($input['current_warehouse_id'])) {
            $this->request->add([
                'previous_warehouse_id' => $input['previous_warehouse_id'],
                'current_warehouse_id' => $input['current_warehouse_id'],
                'account_id' => ItemTransfer::getAccountId()
            ]);
        }
        return $this->request->all();
    }
}
