<?php

namespace App\Http\Requests;

use App\Models\TaxRate;

class TaxRateRequest extends EntityRequest
{

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
                $rules['name'] = 'required|string|max:90|unique:tax_rates,name';
                $rules['rate'] = 'numeric|required';
                $rules['note'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['note'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $taxRate = TaxRate::find((int)request()->segment(2));
                if ($taxRate) {
                    $rules['name'] = 'required|string|max:90|unique:tax_rates,name,' . $taxRate->id . ',id';
                    $rules['rate'] = 'numeric|required';
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
        if (!empty($input['name'])) {
            $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['note'])) {
            $input['note'] = filter_var($input['note'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
