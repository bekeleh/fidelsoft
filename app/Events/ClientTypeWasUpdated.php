<?php

namespace App\Events;

use App\Models\ClientType;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientTypeWasUpdated.
 */
class ClientTypeWasUpdated extends Event
{
    use SerializesModels;

    public $clientType;

    public function __construct(ClientType $clientType)
    {
        $this->clientType = $clientType;
    }
}
