<?php

namespace App\Listeners;

use App\Events\UserWasCreated;
use App\Events\UserWasDeleted;
use App\Events\UserWasUpdated;
use App\Ninja\Transformers\UserTransformer;

/**
 * Class UserListener.
 */
class UserListener extends EntityListener
{

    public function createdUser(UserWasCreated $event)
    {
        $transformer = new UserTransformer($event->User->account);
        $this->checkSubscriptions(EVENT_CREATE_User, $event->User, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function updatedUser(UserWasUpdated $event)
    {
        $transformer = new UserTransformer($event->User->account);
        $this->checkSubscriptions(EVENT_CREATE_User, $event->User, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }

    public function deletedUser(UserWasDeleted $event)
    {
        $transformer = new UserTransformer($event->User->account);
        $this->checkSubscriptions(EVENT_DELETE_User, $event->User, $transformer, [ENTITY_CLIENT, ENTITY_INVOICE]);
    }
}
