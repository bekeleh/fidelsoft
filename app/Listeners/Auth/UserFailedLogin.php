<?php

namespace App\Listeners\Auth;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserFailedLogin
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

    public function handle(Failed $event)
    {
        $now = new Carbon();
        try {
            DB::table('login_attempts')
                ->insert(
                    [
                        'username' => $event->credentials['username'] ?: $event->credentials['email'],
                        'user_agent' => request()->header('UserModel-Agent') ?: null,
                        'remote_ip' => request()->ip(),
                        'successful' => 0,
                        'created_at' => $now,
                    ]
                );
        } catch (Exception $e) {
            $message = $event->user->name . ' can\'t logged in to the application.';
            Storage::put('loginactivity.txt', $message);
        }
    }
}
