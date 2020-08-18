<?php

namespace App\Events\Auth;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserLoggedInEvent.
 */
class UserLoggedInEvent extends Event
{
    use SerializesModels;

    public $user;

    /**
     * UserLoggedInEvent constructor.
     * @param $user
     */
    public function __construct($user = null)
    {
        $this->user = $user;
    }
}
