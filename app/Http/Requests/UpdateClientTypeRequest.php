<?php

namespace App\Http\Requests;

use App\Models\ClientType;

class UpdateClientTypeRequest extends ClientTypeRequest
{
    protected $entityType = ENTITY_CLIENT_TYPE;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $saleType = $this->entity();
        if ($saleType) {
            $rules['name'] = 'required|string|max:90|unique:sale_types,name,' . $saleType->id . ',id,account_id,' . $saleType->account_id;
        }
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
                'account_id' => ClientType::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
