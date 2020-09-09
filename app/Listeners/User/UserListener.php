<?php

namespace App\Listeners\User;

use App\Events\User\UserWasCreatedEvent;
use App\Events\User\UserWasDeletedEvent;
use App\Events\User\UserWasUpdatedEvent;
use App\Ninja\Transformers\UserTransformer;
use App\Listeners\EntityListener;

/**
 * Class UserListener.
 */
class UserListener extends EntityListener
{

    public function createdUser(UserWasCreatedEvent $event)
    {
        $transformer = new UserTransformer($event->user->account);
        $this->checkSubscriptions(EVENT_CREATE_USER, $event->user, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedUser(UserWasUpdatedEvent $event)
    {
        $transformer = new UserTransformer($event->user->account);
        $this->checkSubscriptions(EVENT_CREATE_USER, $event->user, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedUser(UserWasDeletedEvent $event)
    {
        $transformer = new UserTransformer($event->user->account);
        $this->checkSubscriptions(EVENT_DELETE_USER, $event->user, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }
}
