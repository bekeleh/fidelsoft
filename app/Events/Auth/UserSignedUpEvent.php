<?php

namespace App\Events\Auth;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserSignedUpEvent.
 */
class UserSignedUpEvent extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
    }
}
