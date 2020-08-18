<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientWasUpdatedEvent.
 */
class ClientWasUpdatedEvent extends Event
{
    use SerializesModels;

    public $client;


    public function __construct($client)
    {
        $this->client = $client;
    }
}
