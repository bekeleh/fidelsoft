<?php

namespace App\Listeners;

use App\Events\ClientWasCreatedEvent;
use App\Events\ClientWasDeletedEvent;
use App\Events\ClientWasUpdatedEvent;
use App\Ninja\Transformers\ClientTransformer;

/**
 * Class ClientListener.
 */
class ClientListener extends EntityListener
{

    public function createdClient(ClientWasCreatedEvent $event)
    {
        $transformer = new ClientTransformer($event->client->account);

        $this->checkSubscriptions(EVENT_CREATE_CLIENT, $event->client, $transformer);
    }

    public function updatedClient(ClientWasUpdatedEvent $event)
    {
        $transformer = new ClientTransformer($event->client->account);
        $this->checkSubscriptions(EVENT_UPDATE_CLIENT, $event->client, $transformer);
    }

    public function deletedClient(ClientWasDeletedEvent $event)
    {
        $transformer = new ClientTransformer($event->client->account);
        $this->checkSubscriptions(EVENT_DELETE_CLIENT, $event->client, $transformer);
    }
    

}
