<?php

namespace App\Events;

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
