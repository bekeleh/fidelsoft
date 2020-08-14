<?php

namespace App\Providers;

use App\Libraries\Utils;
use App\Models\Client;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class ValidationServiceProvider extends ServiceProvider
{

    public function boot()
    {

        Validator::extend('has_digit', function ($attribute, $value, $parameters, $validator) {

            return Utils::parseFloat($value) > 0;
        });

        Validator::extend('positive', function ($attribute, $value, $parameters) {
            return Utils::parseFloat($value) >= 0;
        });

        Validator::extend('has_credit', function ($attribute, $value, $parameters) {
            $publicClientId = $parameters[0];
            $amount = $parameters[1];

            $client = Client::scope($publicClientId)->firstOrFail();
            $credit = $client->getTotalCredit();

            return $credit >= $amount;
        });

        // check that the time log elements don't overlap
        Validator::extend('time_log', function ($attribute, $value, $parameters) {
            $lastTime = 0;
            $value = json_decode($value);
            array_multisort($value);
            foreach ($value as $timeLog) {
                list($startTime, $endTime) = $timeLog;
                if (!$endTime) {
                    continue;
                }
                if ($startTime < $lastTime || $startTime > $endTime) {
                    return false;
                }
                if ($endTime < min($startTime, $lastTime)) {
                    return false;
                }
                $lastTime = max($lastTime, $endTime);
            }

            return true;
        });

        Validator::extend('has_counter', function ($attribute, $value, $parameters) {
            if (!$value) {
                return true;
            }

            if (strstr($value, '{$counter}') !== false) {
                return true;
            }

            return ((strstr($value, '{$idNumber}') !== false || strstr($value, '{$clientIdNumber}') != false) && (strstr($value, '{$clientCounter}')));
        });

// For a rule to run even when an attribute is empty, the rule must imply that the attribute is required.
// To create such an "implicit" extension, use the Validator::extendImplicit() method
        Validator::extendImplicit('valid_invoice_items',
            function ($attribute, $value, $parameters) {
                $total = 0;
                foreach ($value as $item) {
                    $qty = isset($item['qty']) ? Utils::parseFloat($item['qty']) : 1;
                    $cost = isset($item['cost']) ? Utils::parseFloat($item['cost']) : 1;

                    $total += $qty * $cost;
                }

                return $total <= MAX_INVOICE_AMOUNT;
            });

        Validator::extendImplicit('valid_bill_items',
            function ($attribute, $value, $parameters) {
                $total = 0;
                foreach ($value as $item) {
                    $qty = isset($item['qty']) ? Utils::parseFloat($item['qty']) : 1;
                    $cost = isset($item['cost']) ? Utils::parseFloat($item['cost']) : 1;

                    $total += $qty * $cost;
                }

                return $total <= MAX_INVOICE_AMOUNT;
            });

        Validator::extend('valid_subdomain', function ($attribute, $value, $parameters) {
            return !in_array($value, ['www', 'app', 'mail', 'admin', 'blog', 'user', 'contact', 'payment', 'payments', 'billing', 'invoice', 'business', 'owner', 'info', 'ninja', 'docs', 'doc', 'documents', 'download']);
        });


        // Email array validator
        Validator::extend('email_array', function ($attribute, $value, $parameters, $validator) {
            $value = str_replace(' ', '', $value);
            $array = explode(',', $value);

            foreach ($array as $email) { //loop over values
                $email_to_validate['alert_email'][] = $email;
            }

            $rules = ['alert_email.*' => 'email'];
            $messages = [
                'alert_email.*' => trans('validation.email_array')
            ];

            $validator = Validator::make($email_to_validate, $rules, $messages);

            return $validator->passes();

        });

        // Maybe deleted items have the same instance?
        Validator::extend('unique_undeleted', function ($attribute, $value, $parameters, $validator) {

            if (count($parameters)) {
                $count = DB::table($parameters[0])->select('id')
                    ->where($attribute, '=', $value)
                    ->whereNull('deleted_at')
                    ->where('id', '!=', $parameters[1])
                    ->count();

                return $count < 1;
            }

        });
        //  regex
        Validator::extend('valid_regex', function ($attribute, $value, $parameters, $validator) {

            if ($value !== '') {

                //  Check that the string starts with regex:
                if (strpos($value, 'regex:') === false) {
                    return false;
                }

                $test_string = 'My hovercraft is full of eels';

                // We have to stip out the regex: part here to check with preg_match
                $test_pattern = str_replace('regex:', '', $value);

                try {

                    preg_match($test_pattern, $test_string);
                    return true;
                } catch (Exception $e) {
                    return false;
                }

            }

            return true;

        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

}
