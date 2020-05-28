<?php

namespace App\Http\Requests;

use App\Models\TaxRate;

class UpdateTaxRateRequest extends TaxRateRequest
{
    protected $entityType = ENTITY_TAX_RATE;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $taxRate = $this->entity();
        if ($taxRate)
            $rules['name'] = 'required|unique:tax_rates,name,' . $taxRate->id . ',id,account_id,' . $taxRate->account_id;
        $rules['rate'] = 'required';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

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

    protected function validationData()
    {
        $input = $this->all();
        if (count($input)) {
            $this->request->add([
                'account_id' => TaxRate::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
