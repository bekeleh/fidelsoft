<?php

namespace App\Listeners;

use App\Events\InvoiceWasDeleted;
use App\Events\TaskWasCreated;
use App\Events\TaskWasDeleted;
use App\Events\TaskWasUpdated;
use App\Models\Task;
use App\Ninja\Transformers\TaskTransformer;

/**
 * Class TaskListener.
 */
class TaskListener extends EntityListener
{
    public function createdTask(TaskWasCreated $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_CREATE_TASK, $event->task, $transformer);
    }

    public function updatedTask(TaskWasUpdated $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_UPDATE_TASK, $event->task, $transformer);
    }

    public function deletedTask(TaskWasDeleted $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_DELETE_TASK, $event->task, $transformer);
    }

    public function deletedInvoice(InvoiceWasDeleted $event)
    {
        // Release any tasks associated with the deleted invoice
        Task::where('invoice_id', $event->invoice->id)->update(['invoice_id' => null]);
    }
}
