<?php

namespace App\Http\Requests;

class ItemMovementRequest extends EntityRequest
{
    protected $entityType = ENTITY_STORE;

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

                break;
            }
            case 'PUT':
            case 'PATCH':
            {

            }
            default:
                break;
        }
        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();


        $this->replace($input);
    }
}
