<?php

namespace App\Http\Requests;

use App\Libraries\Utils;
use App\Models\ItemPrice;
use App\Models\Product;
use App\Models\ClientType;

class UpdateItemPriceRequest extends ItemPriceRequest
{
    protected $entityType = ENTITY_ITEM_PRICE;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $itemPrice = $this->entity();
        if ($itemPrice) {
            $rules['product_id'] = 'required|unique:item_prices,product_id,' . $itemPrice->id . ',id,client_type_id,' . $itemPrice->client_type_id . ',account_id,' . $itemPrice->account_id;
        }
        $rules['client_type_id'] = 'required|numeric|exists:client_types,id';
        $rules['item_price'] = 'required|numeric';
        $rules['qty'] = 'numeric';
        $rules['reorder_level'] = 'numeric';
        $rules['EOQ'] = 'numeric';
        $rules['start_date'] = 'required|date';
        $rules['end_date'] = 'required|date|after:start_date';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (count($input)) {
            if (!empty($input['start_date'])) {
                $input['start_date'] = filter_var($input['start_date'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['end_date'])) {
                $input['end_date'] = filter_var($input['end_date'], FILTER_SANITIZE_STRING);
            }
            if (!empty($input['item_price'])) {
                $input['item_price'] = filter_var($input['item_price'], FILTER_SANITIZE_NUMBER_FLOAT);
            }
            if (!empty($input['product_id'])) {
                $input['product_id'] = filter_var($input['product_id'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (!empty($input['client_type_id'])) {
                $input['client_type_id'] = filter_var($input['client_type_id'], FILTER_SANITIZE_NUMBER_INT);
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
        if (!empty($input['client_type_id'])) {
            $input['client_type_id'] = ClientType::getPrivateId($input['client_type_id']);
        }
        if (!empty($input['start_date'])) {
            $input['start_date'] = Utils::toSqlDate($input['start_date']);
        }
        if (!empty($input['end_date'])) {
            $input['end_date'] = Utils::toSqlDate($input['end_date']);
        }
        if (!empty($input['product_id']) && !empty($input['client_type_id'])) {
            $this->request->add([
                'product_id' => $input['product_id'],
                'client_type_id' => $input['client_type_id'],
                'start_date' => $input['start_date'],
                'end_date' => $input['end_date'],
                'account_id' => ItemPrice::getAccountId(),
            ]);
        }

        return $this->request->all();
    }
}
