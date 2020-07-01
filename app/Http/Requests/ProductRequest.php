<?php

namespace App\Http\Requests;

class ProductRequest extends EntityRequest
{
    protected $entityType = ENTITY_PRODUCT;

    public function authorize()
    {
        return true;
    }

    public function entity()
    {
        $product = parent::entity();
        // eager load the itemBrand
        if ($product && !$product->relationLoaded('item_brand')) {
            $product->load(['item_brand']);
        }

        return $product;
    }
}
