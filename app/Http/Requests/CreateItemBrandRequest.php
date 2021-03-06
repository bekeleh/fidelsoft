<?php

namespace App\Http\Requests;

use App\Models\ItemBrand;
use App\Models\ItemCategory;

class CreateItemBrandRequest extends ItemBrandRequest
{
    protected $entityType = ENTITY_ITEM_BRAND;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $rules['item_category_id'] = 'required|numeric';
        $rules['name'] = 'required|max:90|unique:item_brands,name,' . $this->id . ',id,item_category_id,' . $this->item_category_id . ',account_id,' . $this->account_id;
        $rules['notes'] = 'nullable';
        $rules['is_deleted'] = 'boolean';

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
