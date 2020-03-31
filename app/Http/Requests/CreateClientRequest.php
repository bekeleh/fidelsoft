<?php

namespace App\Http\Requests;

class CreateClientRequest extends ClientRequest
{
    protected $entityType = ENTITY_CLIENT;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        if ($this->user()->account->client_number_counter) {
            $rules['id_number'] = 'unique:clients,id_number,,id,account_id,' . $this->user()->account_id;
        }

        return $rules;
    }
}
