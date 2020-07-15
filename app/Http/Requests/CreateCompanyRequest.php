<?php

namespace App\Http\Requests;

class CreateCompanyRequest extends CompanyRequest
{
    protected $entityType = ENTITY_COMPANY;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        return [
            'plan' => 'required',
            'plan_term' => 'required',
        ];
    }
}
