<?php

namespace App\Listeners;

use App\Events\ClientWasCreated;
use App\Events\ClientWasDeleted;
use App\Events\ClientWasUpdated;
use App\Ninja\Transformers\ClientTransformer;

/**
 * Class ClientListener.
 */
class ClientListener extends EntityListener
{

    public function createdClient(ClientWasCreated $event)
    {
        $transformer = new ClientTransformer($event->client->account);

        $this->checkSubscriptions(EVENT_CREATE_CLIENT, $event->client, $transformer);
    }

    public function updatedClient(ClientWasUpdated $event)
    {
        $transformer = new ClientTransformer($event->client->account);
        $this->checkSubscriptions(EVENT_UPDATE_CLIENT, $event->client, $transformer);
    }

    public function deletedClient(ClientWasDeleted $event)
    {
        $transformer = new ClientTransformer($event->client->account);
        $this->checkSubscriptions(EVENT_DELETE_CLIENT, $event->client, $transformer);
    }
    

}
