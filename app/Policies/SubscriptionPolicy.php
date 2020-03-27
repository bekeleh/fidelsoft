<?php

namespace App\Policies;

class SubscriptionPolicy extends EntityPolicy
{
    protected function tableName()
    {
        return 'subscriptions';
    }
}
