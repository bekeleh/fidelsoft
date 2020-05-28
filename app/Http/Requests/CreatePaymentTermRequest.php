<?php

namespace App\Http\Requests;

use App\Models\PaymentTerm;

class CreatePaymentTermRequest extends EntityRequest
{
    protected $entityType = ENTITY_PAYMENT_TERM;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();

        $rules = [];
        $rules['num_days'] = 'required|numeric|unique:payment_terms,num_days,' . $this->id . ',id,account_id,' .
            $this->user()->account_id . ',deleted_at,NULL|unique:payment_terms,num_days,' . $this->id . ',id,account_id,0,deleted_at,NULL';
        $rules['note'] = 'nullable';
        $rules['is_deleted'] = 'boolean';
        $rules['note'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (!empty($input['num_days'])) {
            $input['num_days'] = filter_var($input['num_days'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['note'])) {
            $input['note'] = filter_var($input['note'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
