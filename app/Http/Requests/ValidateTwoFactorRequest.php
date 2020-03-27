<?php

namespace App\Http\Requests;

use App\Models\User;
use Google2FA;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Factory as ValidatonFactory;

class ValidateTwoFactorRequest extends Request
{
    public function authorize()
    {
        try {
            $this->user = User::findOrFail(
                session('2fa:user:id')
            );
        } catch (\Exception $exc) {
            return false;
        }

        return true;
    }

    public function __construct(ValidatonFactory $factory)
    {
        $factory->extend(
            'valid_token',
            function ($attribute, $value, $parameters, $validator) {
                $secret = Crypt::decrypt($this->user->google_2fa_secret);

                return Google2FA::verifyKey($secret, $value);
            },
            trans('texts.invalid_code')
        );

        $factory->extend(
            'used_token',
            function ($attribute, $value, $parameters, $validator) {
                $key = $this->user->id . ':' . $value;

                return !Cache::has($key);
            },
            trans('texts.invalid_code')
        );
    }

    public function rules()
    {
        return [
            'totp' => 'bail|required|digits:6|valid_token|used_token',
        ];
    }
}
