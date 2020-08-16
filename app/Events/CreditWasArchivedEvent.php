<?php

namespace App\Events;

use App\Models\Credit;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreditWasArchivedEvent.
 */
class CreditWasArchivedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Client
     */
    public $credit;

    /**
     * Create a new event instance.
     *
     * @param Credit $credit
     */
    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
    }
}
