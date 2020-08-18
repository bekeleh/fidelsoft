<?php

namespace App\Events\Client;

use App\Events\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientWasArchivedEvent.
 */
class ClientWasArchivedEvent extends Event
{
    use Dispatchable, SerializesModels;


    public $client;

    /**
     * Create a new event instance.
     *
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }
}
