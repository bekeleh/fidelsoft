<?php

namespace App\Http\Requests;

use App\Models\ItemTransfer;
use App\Models\Store;

class ItemTransferRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_TRANSFER;

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
                $rules['item_id'] = 'required|array';
                $rules['current_store_id'] = 'required|numeric|exists:stores';
                $rules['previous_store_id'] = 'required|numeric|exists:stores';
//                $rules['approver_id'] = 'required|numeric|exists:approval_statuses';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $itemTransfer = ItemTransfer::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
                if ($itemTransfer) {
                    $rules['item_id'] = 'required|array';
                    $rules['current_store_id'] = 'required|numeric|exists:stores';
                    $rules['previous_store_id'] = 'required|numeric|exists:stores';
//                    $rules['approver_id'] = 'required|numeric|exists:approval_statuses';
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
            if (!empty($input['item_id'])) {
                $input['item_id'] = filter_var($input['item_id'], FILTER_SANITIZE_NUMBER_INT);
            }
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

        if (!empty($input['store_id'])) {
            $input['store_id'] = Store::getPrivateId($input['store_id']);
        }
        if (!empty($input['item_id']) && !empty($input['store_id'])) {
            $this->request->add([
                'previous_store_id' => $input['previous_store_id'],
                'current_store_id' => $input['current_store_id'],
                'account_id' => ItemTransfer::getAccountId()
            ]);
        }
        return $this->request->all();
    }
}
