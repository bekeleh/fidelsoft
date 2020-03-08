<?php

namespace App\Http\Requests;

use App\Models\User;

class UserRequest extends EntityRequest
{
    protected $entityType = ENTITY_USER;

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
                $rules['first_name'] = 'required|max:50';
                $rules['last_name'] = 'required|max:50';
                $rules['username'] = 'required|max:50|unique:users,username';
                $rules['email'] = 'required|email|max:50|unique:users,email';
                $rules['notes'] = 'nullable';
                $rules['is_deleted'] = 'boolean';
                $rules['notes'] = 'nullable';
                break;
            }
            case 'PUT':
            case 'PATCH':
            {
                $user = User::where('public_id', (int)request()->segment(2))->first();
                if ($user) {
                    $rules['first_name'] = 'required|max:50';
                    $rules['last_name'] = 'required|max:50';
                    $rules['username'] = 'required|max:50|unique:users,username,' . $user->id . ',id';
                    $rules['email'] = 'required|email|max:50|unique:users,email';
                    $rules['is_deleted'] = 'boolean';
                    $rules['notes'] = 'nullable';
                    break;
                } else {
                    return;
                }
            }
            default:
                break;
        }
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
        if (!empty($input['notes'])) {
            $input['notes'] = filter_var($input['notes'], FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
