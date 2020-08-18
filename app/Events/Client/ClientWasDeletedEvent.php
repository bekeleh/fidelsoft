<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientWasDeletedEvent.
 */
class ClientWasDeletedEvent extends Event
{
    use SerializesModels;


    public $client;


    public function __construct($client)
    {
        $this->client = $client;
    }
}
