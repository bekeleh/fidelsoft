<?php

namespace App\Http\Requests;

use App\Models\User;

class CreateVendorContactRequest extends VendorContactRequest
{
    protected $entityType = ENTITY_VENDOR_CONTACT;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];

        $rules['name'] = 'required|max:191|unique:contacts,name,' . $this->id . ',id,account_id,' . $this->account_id;
        $rules['is_deleted'] = 'boolean';
        $rules['private_notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        if (!empty($input['name'])) {
            $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        }

        if (!empty($input['private_notes'])) {
            $input['private_notes'] = filter_var($input['private_notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    public function validationData()
    {
        $input = $this->input();

        if (count($input)) {
            $this->request->add([
                'account_id' => User::getAccountId(),
            ]);
        }
        return $this->request->all();
    }
}
