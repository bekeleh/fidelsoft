<?php

namespace App\Events\Setting;

use App\Events\Event;
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

    public function __construct(Project $project)
    {
        $this->project = $project;
    }
}
