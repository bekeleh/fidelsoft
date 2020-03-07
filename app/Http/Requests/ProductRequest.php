<?php

namespace App\Http\Requests;

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
                $rules['product_key'] = 'required|max:90|unique:products,product_key';
                $rules['note'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['note'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $product = Product::where('public_id', (int)request()->segment(2))->first();
                if ($product) {
                    $rules['product_key'] = 'required|max:90|unique:products,product_key,' . $product->id . ',public_id';
                    $rules['is_deleted'] = 'boolean';
                    $rules['note'] = 'nullable';
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
        if (!empty($input['product_key'])) {
            $input['product_key'] = filter_var($input['product_key'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['note'])) {
            $input['note'] = filter_var($input['note'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
