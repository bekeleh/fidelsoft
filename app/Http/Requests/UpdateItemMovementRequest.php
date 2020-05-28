<?php

namespace App\Http\Requests;

class UpdateItemMovementRequest extends ItemMovementRequest
{
    protected $entityType = ENTITY_ITEM_MOVEMENT;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();

        $rules = [];
        $rules['qty'] = 'numeric';
        $rules['qoh'] = 'numeric';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (!empty($input)) {
            $input['qty'] = filter_var($input['qty'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
