<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\Store;
use App\Models\User;

class UpdateUserRequest extends UserRequest
{
    protected $entityType = ENTITY_USER;

    public function authorize()
    {
        return $this->user()->can('edit', ENTITY_USER);
    }

    public function rules()
    {
        $this->sanitize();
        $this->validationData();

        $rules = [];
        $user = $this->entity();
        if ($user) {
            $rules['username'] = 'required|max:50|unique:users,username,' . $user->id . ',id,account_id,' . $user->account_id;
            $rules['email'] = 'required|email|max:50|unique:users,email,' . $user->id . ',id';
        }
        $rules['first_name'] = 'required|max:50';
        $rules['last_name'] = 'required|max:50';
        $rules['permission_groups'] = 'required|array';
        $rules['location_id'] = 'required|exists:locations,id';
        $rules['store_id'] = 'required|exists:stores,id';
        $rules['is_deleted'] = 'boolean';
        $rules['notes'] = 'nullable';

        return $rules;
    }

    public function sanitize()
    {
        $input = $this->all();
        if (!empty($input['first_name'])) {
            $input['first_name'] = filter_var($input['first_name'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['last_name'])) {
            $input['last_name'] = filter_var($input['last_name'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['email'])) {
            $input['email'] = filter_var($input['email'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['username'])) {
            $input['username'] = filter_var($input['username'], FILTER_SANITIZE_STRING);
        }
        if (!empty($input['location_id'])) {
            $input['location_id'] = filter_var($input['location_id'], FILTER_SANITIZE_NUMBER_INT);
        }
        if (!empty($input['store_id'])) {
            $input['store_id'] = filter_var($input['store_id'], FILTER_SANITIZE_NUMBER_INT);
        }
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }

    public function validationData()
    {
        $input = $this->input();
        if (!empty($input['location_id'])) {
            $input['location_id'] = Location::getPrivateId($input['location_id']);
        }
        if (!empty($input['store_id'])) {
            $input['store_id'] = Store::getPrivateId($input['store_id']);
        }
        if (!empty($input['location_id']) && !empty($input['store_id'])) {
            $this->request->add([
                'location_id' => $input['location_id'],
                'store_id' => $input['store_id'],
                'account_id' => User::getAccountId(),
            ]);
        }

        return $this->request->all();
    }
}
