<?php

namespace App\Events;

use App\Models\User;
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
    public function __construct($user)
    {
        $this->user = $user;
    }
}
