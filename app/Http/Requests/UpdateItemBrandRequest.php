<?php

namespace App\Http\Requests;

use App\Models\ItemBrand;
use App\Models\ItemCategory;

class UpdateItemBrandRequest extends ItemBrandRequest
{
    protected $entityType = ENTITY_ITEM_BRAND;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $itemBrand = $this->entity();
        if ($itemBrand) {
            $rules['name'] = 'required|max:90|unique:item_brands,name,' . $itemBrand->id . ',id,item_category_id,' . $itemBrand->item_category_id . ',account_id,' . $itemBrand->account_id;
        }
        $rules['item_category_id'] = 'required|numeric';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (!empty($input['item_category_id'])) {
            $input['item_category_id'] = filter_var($input['item_category_id'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['name'])) {
            $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    public function validationData()
    {
        $input = $this->input();
        if (count($input)) {
            if (!empty($input['item_category_id'])) {
                $input['item_category_id'] = ItemCategory::getPrivateId($input['item_category_id']);
            }
            if (!empty($input['item_category_id'])) {
                $this->request->add([
                    'account_id' => ItemBrand::getAccountId(),
                    'item_category_id' => $input['item_category_id'],
                ]);
            }
        }
        return $this->request->all();
    }
}
