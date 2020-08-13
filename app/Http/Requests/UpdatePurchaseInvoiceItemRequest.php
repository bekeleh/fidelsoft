<?php

namespace App\Http\Requests;

class UpdateBillItemRequest extends BillItemRequest
{
    protected $entityType = ENTITY_BILL_ITEM;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }
}
