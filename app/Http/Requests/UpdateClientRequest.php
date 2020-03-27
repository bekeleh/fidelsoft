<?php

namespace App\Http\Requests;

class UpdateClientRequest extends ClientRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (!$this->entity()) {
            return [];
        }

        $rules = [];

        if ($this->user()->account->client_number_counter) {
            $rules['id_number'] = 'unique:clients,id_number,' . $this->entity()->id . ',id,account_id,' . $this->user()->account_id;
        }

        return $rules;
    }
}
