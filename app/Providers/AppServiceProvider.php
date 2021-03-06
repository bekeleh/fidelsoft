<?php

namespace App\Providers;

use App\Notifications\SlackFailedJob;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobFailed;

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

        $slackUrl = env('SLACK_URL', '');
        Queue::failing(function (JobFailed $event) use ($slackUrl) {
            Notification::route('slack', $slackUrl)->notify(new SlackFailedJob($event));
        });

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
        $this->app->bind('Illuminate\Contracts\Auth\Registrar', 'App\Services\Registrar');
    }
}
