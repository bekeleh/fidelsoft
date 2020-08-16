<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserLoggedIn.
 */
class UserLoggedIn extends Event
{
    use SerializesModels;

    public $user;

    /**
     * UserLoggedIn constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
