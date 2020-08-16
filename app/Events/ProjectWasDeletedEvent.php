<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProjectWasDeletedEvent.
 */
class ProjectWasDeletedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Prooject
     */
    public $project;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }
}
