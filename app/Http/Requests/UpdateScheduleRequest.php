<?php

namespace App\Http\Requests;

use App\Models\Schedule;

class UpdateScheduleRequest extends ScheduleRequest
{
    protected $entityType = ENTITY_SCHEDULE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
//        $ScheduledReport = $this->entity();
//        if ($ScheduledReport)
//            $rules['name'] = 'required|unique:scheduled_reports,name,' . $ScheduledReport->id . ',id,account_id,' . $ScheduledReport->account_id;

        $rules['title'] = 'required';
        $rules['description'] = 'required';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        if (!empty($input['title'])) {
            $input['title'] = filter_var($input['title'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['description'])) {
            $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    protected function validationData()
    {
        $input = $this->all();

        if (count($input)) {
            $this->request->add([
                'account_id' => Schedule::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
