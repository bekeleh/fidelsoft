<?php

namespace App\Http\Requests;

use App\Models\Status;

class UpdateStatusRequest extends StatusRequest
{
    protected $entityType = ENTITY_STATUS;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $Status = $this->entity();
        if ($Status) {
            $rules['name'] = 'required|max:90|unique:statuses,name,' . $Status->id . ',id,account_id,' . $Status->account_id;
        }
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

    public function validationData()
    {
        $input = $this->input();
        if (count($input)) {
            $this->request->add([
                'account_id' => Status::getAccountId(),
            ]);
        }

        return $this->request->all();
    }
}
