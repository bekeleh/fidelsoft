<?php

namespace App\Http\Requests;

use App\Libraries\Utils;
use Illuminate\Http\Request as InputRequest;
use Illuminate\Support\Facades\Response;

class RegisterRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function __construct(InputRequest $req)
    {
        $this->req = $req;
    }

    public function rules()
    {
        $rules = [
            'email' => 'email|required|unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required',
        ];

        return $rules;
    }

    public function response(array $errors)
    {
        /* If the user is not validating from a mobile app - pass through parent::response */
        if (!isset($this->req->api_secret)) {
            return parent::response($errors);
        }
        /* If the user is validating from a mobile app - pass through first error string and return error */
        foreach ($errors as $error) {
            foreach ($error as $key => $value) {
                $message['error'] = ['message' => $value];
                $message = json_encode($message, JSON_PRETTY_PRINT);
                $headers = Utils::getApiHeaders();

                return Response::make($message, 400, $headers);
            }
        }
    }
}
