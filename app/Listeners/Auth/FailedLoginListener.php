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
        if (!empty($event->credentials)) {
            $now = new Carbon();
            DB::table('login_attempts')
                ->insert(
                    [
                        'username' => isset($event->credentials['username']) ?: $event->credentials['email'],
                        'user_agent' => request()->header('UserModel-Agent') ?: null,
                        'remote_ip' => request()->ip(),
                        'successful' => false,
                        'created_at' => $now,
                    ]
                );
        }
    }
}
