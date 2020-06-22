<?php

namespace App\Http\Requests;

use App\Models\ItemBrand;
use App\Models\Product;

class CreateProductRequest extends ProductRequest
{
    protected $entityType = ENTITY_PRODUCT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
//        $rules['product_key'] = 'required|unique:products,product_key,' . $this->id . ',id,item_brand_id,' . $this->item_brand_id . ',account_id,' . $this->account_id;
        $rules['product_key'] = 'required|unique:products,product_key,' . $this->id . ',id,account_id,' . $this->account_id;
        $rules['item_brand_id'] = 'required|numeric';
        $rules['tax_category_id'] = 'required|numeric';
        $rules['category_id'] = 'required|numeric';
        $rules['barcode'] = 'nullable';
        $rules['item_tag'] = 'nullable';
        $rules['unit_id'] = 'required|numeric';
        $rules['cost'] = 'required|numeric';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (!empty($input['product_key'])) {
            $input['product_key'] = filter_var($input['product_key'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['cost'])) {
            $input['cost'] = filter_var($input['cost'], FILTER_SANITIZE_NUMBER_FLOAT);
        }

        $this->replace($input);
    }

    protected function validationData()
    {
        $input = $this->all();
        if (isset($input['item_brand_id']) && $input['item_brand_id']) {
            $input['item_brand_id'] = ItemBrand::getPrivateId($input['item_brand_id']);
        }

        if (!empty($input['item_brand_id'])) {
            $this->request->add([
                'item_brand_id' => $input['item_brand_id'],
                'account_id' => Product::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
