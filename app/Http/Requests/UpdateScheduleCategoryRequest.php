<?php

namespace App\Http\Requests;

use App\Models\ScheduleCategory;

class UpdateScheduleCategoryRequest extends ScheduleCategoryRequest
{
    protected $entityType = ENTITY_SCHEDULE_CATEGORY;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        $this->validationData();

        $scheduleCategory = ScheduleCategory::where('public_id', (int)request()->segment(2))->where('account_id', $this->account_id)->first();
        if ($scheduleCategory)
            $rules['name'] = 'required|unique:schedule_categories,name,' . $scheduleCategory->id . ',id,account_id,' . $scheduleCategory->account_id;

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
                'account_id' => ScheduleCategory::getAccountId()
            ]);
        }

        return $this->request->all();
    }
}
