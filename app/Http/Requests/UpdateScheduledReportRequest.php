<?php

namespace App\Http\Requests;

use App\Models\ScheduleCategory;

class UpdateScheduledReportRequest extends ScheduledReportRequest
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
//        $ScheduledReport = ScheduleCategory::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
//        if ($ScheduledReport)
//            $rules['name'] = 'required|unique:scheduled_reports,name,' . $ScheduledReport->id . ',id,account_id,' . $ScheduledReport->account_id;

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
                'account_id' => ScheduleCategory::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
