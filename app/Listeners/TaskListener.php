<?php

namespace App\Listeners;

use App\Events\InvoiceWasDeletedEvent;
use App\Events\TaskWasCreatedEvent;
use App\Events\TaskWasDeletedEvent;
use App\Events\TaskWasUpdatedEvent;
use App\Models\Task;
use App\Ninja\Transformers\TaskTransformer;
use App\Listeners\Common\EntityListener;

/**
 * Class TaskListener.
 */
class TaskListener extends EntityListener
{
    public function createdTask(TaskWasCreatedEvent $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_CREATE_TASK, $event->task, $transformer);
    }

    public function updatedTask(TaskWasUpdatedEvent $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_UPDATE_TASK, $event->task, $transformer);
    }

    public function deletedTask(TaskWasDeletedEvent $event)
    {
        $transformer = new TaskTransformer($event->task->account);
        $this->checkSubscriptions(EVENT_DELETE_TASK, $event->task, $transformer);
    }

    public function deletedInvoice(InvoiceWasDeletedEvent $event)
    {
        // Release any tasks associated with the deleted invoice
        Task::where('invoice_id', $event->invoice->id)->update(['invoice_id' => null]);
    }
}
