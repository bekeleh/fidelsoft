<?php

namespace App\Http\Requests;

use App\Models\TaxRate;

class TaxRateRequest extends EntityRequest
{

    protected $entityType = ENTITY_TAX_RATE;

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
//                $this->validationData();
                $rules['name'] = 'required|unique:tax_rates,name,' . $this->id . ',id,account_id,' . $this->account_id;
                $rules['rate'] = 'required';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
//                $this->validationData();
                $taxRate = TaxRate::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
                if ($taxRate) {
                    $rules['name'] = 'required|string|max:90|unique:tax_rates,name,' . $taxRate->id . ',id,account_id,' . $taxRate->account_id;
                    $rules['rate'] = 'required';
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
