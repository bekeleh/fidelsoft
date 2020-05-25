<?php

namespace App\Http\Requests;

use App\Models\ExpenseCategory;

class CreateScheduledReportRequest extends ScheduledReportRequest
{
    protected $entityType = ENTITY_SCHEDULED_REPORT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();
        $rules = [];
        $rules['name'] = 'required';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        if (!empty($input['ip'])) {
            $input['ip'] = filter_var($input['ip'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    protected function validationData()
    {
        $input = $this->all();

        if (count($input)) {
            $this->request->add([
                'account_id' => ExpenseCategory::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
