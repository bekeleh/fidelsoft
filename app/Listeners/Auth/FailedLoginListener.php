<?php

namespace App\Listeners\Auth;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FailedLoginListener
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
//                        'username' => $event->credentials['username'] ?: $event->credentials['email'],
                        'user_agent' => request()->header('UserModel-Agent') ?: null,
                        'remote_ip' => request()->ip(),
                        'successful' => 0,
                        'created_at' => $now,
                    ]
                );
        } catch (Exception $e) {
//            $user = $event->credentials['username'] ?: $event->credentials['email'];
            $remote_ip = request()->ip();
            $message = $remote_ip . ' can\'t logged in to the application.';
            Storage::put('failed-logins.txt', $message);
        }
    }
}
