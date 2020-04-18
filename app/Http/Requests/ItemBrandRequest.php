<?php

namespace App\Http\Requests;

use App\Models\ItemBrand;
use App\Models\ItemCategory;

class ItemBrandRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_BRAND;

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
                $rules['item_category_id'] = 'required|numeric';
                $rules['name'] = 'required|max:90|unique:item_brands,name,' . $this->id . ',id,item_category_id,' . $this->item_category_id . ',account_id,' . $this->account_id;
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $this->validationData();
                $itemBrand = ItemBrand::where('public_id', (int)request()->segment(2))->first();
                if ($itemBrand) {
                    $rules['item_category_id'] = 'required|numeric';
                    $rules['name'] = 'required|max:90|unique:item_brands,name,' . $itemBrand->id . ',id,item_category_id,' . $itemBrand->item_category_id . ',account_id,' . $itemBrand->account_id;
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
