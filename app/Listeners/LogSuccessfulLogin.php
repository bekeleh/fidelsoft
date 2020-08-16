<?php

namespace App\Listeners;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(Login $event)
    {
        try {
            $now = new Carbon();
            DB::table('login_attempts')->insert(
                [
                    'username' => $event->user->username,
                    'user_agent' => request()->header('UserModel-Agent'),
                    'remote_ip' => request()->ip(),
                    'successful' => 1,
                    'created_at' => $now,
                ]
            );
        } catch (Exception $e) {
            $message = $event->user->name . ' just logged in to the application.';
            Storage::put('loginactivity.txt', $message);
        }
    }
}
