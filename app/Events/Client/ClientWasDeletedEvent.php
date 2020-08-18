<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientWasDeletedEvent.
 */
class ClientWasDeletedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $client;


    public function __construct($client)
    {
        $this->client = $client;
    }
}
