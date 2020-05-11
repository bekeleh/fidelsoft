<?php

namespace App\Http\Requests;

class UpdateManufacturerRequest extends EntityRequest
{
    protected $entityType = ENTITY_MANUFACTURER;

    public function authorize()
    {
        return $this->user()->can('edit', ENTITY_MANUFACTURER);
    }


    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
