<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;

class GenerateCalendarEvents extends Job
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $events = [];
        $filter = request()->filter ?: [];

        $data = [
            ENTITY_INVOICE => Invoice::Scope()->invoices(),
            ENTITY_QUOTE => Invoice::Scope()->quotes(),
            ENTITY_TASK => Task::Scope()->with(['project']),
            ENTITY_PAYMENT => Payment::Scope()->with(['invoice']),
            ENTITY_EXPENSE => Expense::Scope()->with(['expense_category']),
            ENTITY_PROJECT => Project::Scope(),
        ];

        foreach ($data as $type => $source) {
            if (! count($filter) || in_array($type, $filter)) {
                $source->where(function($query) use ($type) {
                    $start = date_create(request()->start);
                    $end = date_create(request()->end);
                    return $query->dateRange($start, $end);
                });

                foreach ($source->with(['account', 'client.contacts'])->get() as $entity) {
                    if ($entity->client && $entity->client->trashed()) {
                        continue;
                    }

                    $subColors = count($filter) == 1;
                    $events[] = $entity->present()->calendarEvent($subColors);
                }
            }
        }

        return $events;
    }
}
