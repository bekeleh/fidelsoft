<?php

namespace App\Listeners;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
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
                        'username' => $event->credentials['email'],
                        'user_agent' => request()->header('UserModel-Agent') ?: null,
                        'remote_ip' => request()->ip(),
                        'successful' => 0,
                        'created_at' => $now,
                    ]
                );
        } catch (Exception $e) {
            throw new Exception('Sorry, somethings went wrong,try again later.');
        }
    }
}
