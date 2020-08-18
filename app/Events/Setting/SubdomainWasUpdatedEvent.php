<?php

namespace App\Events\Setting;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class SubdomainWasUpdatedEvent extends Event
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
