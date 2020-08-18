<?php

namespace App\Events\User;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserWasCreatedEvent.
 */
class UserWasCreatedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $user;


    public function __construct($user)
    {
        $this->user = $user;
    }
}
