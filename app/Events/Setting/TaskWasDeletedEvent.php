<?php

namespace App\Events\Setting;

use App\Events\Event;
use App\Models\Task;
use Illuminate\Queue\SerializesModels;

/**
 * Class TaskWasDeletedEvent.
 */
class TaskWasDeletedEvent extends Event
{
    use SerializesModels;

    /**
     * @var Task
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
