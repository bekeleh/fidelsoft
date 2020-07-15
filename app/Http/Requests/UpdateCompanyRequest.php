<?php

namespace App\Http\Requests;

class UpdateCompanyRequest extends CompanyRequest
{
    protected $entityType = ENTITY_COMPANY;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        return [
            'plan' => 'required',
            'plan_term' => 'required',
        ];
    }
}
