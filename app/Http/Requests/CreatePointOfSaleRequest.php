<?php

namespace App\Http\Requests;

use App\Models\ItemBrand;
use App\Models\Product;
use App\Models\Unit;

class CreatePointOfSaleRequest extends PointOfSaleRequest
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

        $rules['name'] = 'required|unique:products,name,' . $this->id . ',id,item_brand_id,' . $this->item_brand_id . ',account_id,' . $this->account_id;
        $rules['item_brand_id'] = 'required|numeric';
        $rules['barcode'] = 'nullable';
        $rules['item_tag'] = 'nullable';
        $rules['unit_id'] = 'required|numeric';
        $rules['item_cost'] = 'required|numeric';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';
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
        if (isset($input['item_brand_id']) && $input['item_brand_id']) {
            $input['item_brand_id'] = ItemBrand::getPrivateId($input['item_brand_id']);
        }
        if (isset($input['unit_id']) && $input['unit_id']) {
            $input['unit_id'] = Unit::getPrivateId($input['unit_id']);
        }
        if (!empty($input['item_brand_id']) && !empty($input['unit_id'])) {
            $this->request->add([
                'item_brand_id' => $input['item_brand_id'],
                'unit_id' => $input['unit_id'],
                'account_id' => Product::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
