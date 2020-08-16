<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class SubdomainWasRemovedEvent extends Event
{
    use SerializesModels;
    public $account;

    /**
     * Create a new event instance.
     *
     * @param $account
     */
    public function __construct($account)
    {
        $this->account = $account;
    }
}
