<?php

namespace App\Http\Requests;

use App\Models\ItemCategory;
use App\Models\Product;

class ProductRequest extends EntityRequest
{
    protected $entityType = ENTITY_PRODUCT;

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
                $input = $this->all();
                $itemCategoryId = ItemCategory::getPrivateId($input['item_category_id']);
                $rules['name'] = 'required|unique:products,name,' . $this->id . ',id,item_category_id,' . $itemCategoryId;
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
                $product = Product::where('public_id', (int)request()->segment(2))->first();
                if ($product) {
                    $rules['name'] = 'required|unique:products,name,' . $product->id . ',id,item_category_id,' . $product->item_category_id;
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
}
