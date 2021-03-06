<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientTypeWasUpdatedEvent.
 */
class ClientTypeWasUpdatedEvent extends Event
{
    use Dispatchable, SerializesModels;

    public $clientType;

    public function __construct($clientType)
    {
        $this->clientType = $clientType;
    }
}
