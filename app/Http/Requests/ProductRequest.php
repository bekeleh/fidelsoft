<?php

namespace App\Http\Requests;

use App\Models\ItemCategory;
use App\Models\Product;
use App\Models\Unit;

class ProductRequest extends EntityRequest
{
    protected $entityType = ENTITY_PRODUCT;

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
                $rules['name'] = 'required|unique:products,name,' . $this->id . ',id,item_category_id,' . $this->item_category_id . ',account_id,' . $this->account_id;
                $rules['item_category_id'] = 'required|numeric';
                $rules['barcode'] = 'nullable';
                $rules['item_tag'] = 'nullable';
                $rules['unit_id'] = 'required|numeric';
                $rules['item_cost'] = 'required|numeric';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $product = Product::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
                if ($product) {
                    $rules['name'] = 'required|unique:products,name,' . $product->id . ',id,item_category_id,' . $product->item_category_id . ',account_id,' . $product->account_id;
                    $rules['item_category_id'] = 'required|numeric';
                    $rules['barcode'] = 'nullable';
                    $rules['item_tag'] = 'nullable';
                    $rules['unit_id'] = 'required|numeric';
                    $rules['item_cost'] = 'required|numeric';
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
        if (!empty($input['name'])) {
            $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    protected function validationData()
    {
        $input = $this->all();
        if (isset($input['item_category_id']) && $input['item_category_id']) {
            $input['item_category_id'] = ItemCategory::getPrivateId($input['item_category_id']);
        }
        if (isset($input['unit_id']) && $input['unit_id']) {
            $input['unit_id'] = Unit::getPrivateId($input['unit_id']);
        }
        if (!empty($input['item_category_id']) && !empty($input['unit_id'])) {
            $this->request->add([
                'item_category_id' => $input['item_category_id'],
                'unit_id' => $input['unit_id'],
                'account_id' => Product::getAccountId()
            ]);
        }
        return $this->request->all();
    }
}
