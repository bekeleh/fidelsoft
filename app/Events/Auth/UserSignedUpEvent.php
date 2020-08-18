<?php

namespace App\Events\Auth;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserSignedUpEvent.
 */
class UserSignedUpEvent extends Event
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
    }
}
