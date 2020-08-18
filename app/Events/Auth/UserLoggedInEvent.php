<?php

namespace App\Events\Auth;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserLoggedInEvent.
 */
class UserLoggedInEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $user;


    public function __construct($user = null)
    {
        $this->user = $user;
    }
}
