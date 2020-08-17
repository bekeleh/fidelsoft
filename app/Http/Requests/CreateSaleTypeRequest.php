<?php

namespace App\Http\Requests;

use App\Models\Setting\SaleType;

class CreateSaleTypeRequest extends SaleTypeRequest
{
    protected $entityType = ENTITY_SALE_TYPE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $rules['name'] = 'required|string|max:90|unique:sale_types,name,' . $this->id . ',id,account_id,' . $this->account_id;
        $rules['note'] = 'nullable';
        $rules['is_deleted'] = 'boolean';
        $rules['note'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (!empty($input['name'])) {
            $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['note'])) {
            $input['note'] = filter_var($input['note'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    protected function validationData()
    {
        $input = $this->all();

        if (count($input)) {
            $this->request->add([
                'account_id' => SaleType::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
