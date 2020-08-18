<?php

namespace App\Listeners\Report;

use App\Events\Client\ClientWasCreatedEvent;
use App\Events\Client\ClientWasDeletedEvent;
use App\Events\Client\ClientWasUpdatedEvent;
use App\Ninja\Transformers\ClientTransformer;
use App\Listeners\EntityListener;

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
