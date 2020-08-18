<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\Proposal;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProposalWasDeletedEvent.
 */
class ProposalWasDeletedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Proposal
     */
    public $proposal;

    public function __construct(Proposal $proposal)
    {
        $this->proposal = $proposal;
    }
}
