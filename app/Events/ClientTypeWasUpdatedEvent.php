<?php

namespace App\Events;

use App\Models\Setting\ClientType;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientTypeWasUpdatedEvent.
 */
class ClientTypeWasUpdatedEvent extends Event
{
    use SerializesModels;

    public $clientType;

    public function __construct(ClientType $clientType)
    {
        $this->clientType = $clientType;
    }
}
