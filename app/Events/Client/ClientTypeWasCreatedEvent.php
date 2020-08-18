<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientTypeWasCreatedEvent.
 */
class ClientTypeWasCreatedEvent extends Event
{
    use SerializesModels;

    public $clientType;

    public function __construct($clientType)
    {
        $this->clientType = $clientType;
    }
}
