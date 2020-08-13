<?php

namespace App\Http\Requests;

class CreateBillItemRequest extends BillItemRequest
{
    protected $entityType = ENTITY_BILL_ITEM;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }
}
