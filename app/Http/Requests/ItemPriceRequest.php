<?php

namespace App\Http\Requests;

use App\Models\ItemPrice;
use Google\Auth\Cache\Item;

class ItemPriceRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_PRICE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        dd($this->all());
        switch ($this->method()) {
            case 'POST':
            {
                $rules['product_id'] = 'required|numeric';
                $rules['sale_type_id'] = 'required|numeric';
                $rules['price'] = 'required|numeric';
                $rules['qty'] = 'numeric';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $itemPrice = ItemPrice::find((int)request()->segment(2));
                dd($itemPrice);
                $rules['product_id'] = 'required|numeric';
                $rules['sale_type_id'] = 'required|numeric';
                $rules['price'] = 'required|numeric';
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
        if (!empty($input['start_date'])) {
            $input['start_date'] = filter_var($input['start_date'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['end_date'])) {
            $input['end_date'] = filter_var($input['end_date'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['price'])) {
            $input['price'] = filter_var($input['price'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['product_id'])) {
            $input['product_id'] = filter_var($input['product_id'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['sale_type_id'])) {
            $input['sale_type_id'] = filter_var($input['sale_type_id'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
