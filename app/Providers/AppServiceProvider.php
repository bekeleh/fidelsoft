<?php

namespace App\Providers;

use App\Http\Mixins\FormsMixin;
use App\Http\Mixins\RulesMixin;
use App\Libraries\Utils;
use App\Models\Client;
use Form;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Route::singularResourceParameters(false);

        // support selecting job database
        Queue::before(function (JobProcessing $event) {
            $body = $event->job->getRawBody();
            preg_match('/db-ninja-[\d+]/', $body, $matches);
            if (count($matches)) {
                config(['database.default' => $matches[0]]);
            }
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

        Validator::extendImplicit('valid_invoice_items', function ($attribute, $value, $parameters) {
            $total = 0;
            foreach ($value as $item) {
                $qty = !empty($item['qty']) ? Utils::parseFloat($item['qty']) : 1;
                $cost = !empty($item['cost']) ? Utils::parseFloat($item['cost']) : 1;
                $total += $qty * $cost;
            }

            return $total <= MAX_INVOICE_AMOUNT;
        });

        Validator::extend('valid_subdomain', function ($attribute, $value, $parameters) {
            return !in_array($value, ['www', 'app', 'mail', 'admin', 'blog', 'user', 'contact', 'payment', 'payments', 'billing', 'invoice', 'business', 'owner', 'info', 'ninja', 'docs', 'doc', 'documents', 'download']);
        });

        Rule::mixin(new RulesMixin());
        Form::mixin(new FormsMixin());
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'App\Services\Registrar'
        );
    }
}
