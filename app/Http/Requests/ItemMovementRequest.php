<?php

namespace App\Http\Requests;

class ItemMovementRequest extends EntityRequest
{
    protected $entityType = ENTITY_ITEM_MOVEMENT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $rules = [];
        switch ($this->method()) {
            case 'POST':
            {
                $rules['qty'] = 'numeric';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $rules['qty'] = 'numeric';
                $rules['qoh'] = 'numeric';
                break;
            }
            default:
                break;
        }
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
