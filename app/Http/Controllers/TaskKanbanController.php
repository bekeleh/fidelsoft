<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;

class TaskKanbanController extends BaseController
{

    public function index($clientPublicId = false, $projectPublicId = false)
    {
        $tasks = Task::Scope()
            ->with(['project', 'client', 'task_status'])
            ->whereNull('invoice_id')
            ->orderBy('task_status_sort_order')
            ->orderBy('id')
            ->get();

        $statuses = TaskStatus::Scope()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $projects = Project::Scope()->with('client')->get();
        $clients = Client::Scope()->with(['contacts'])->get();

        // check initial statuses exist
        if (!$statuses->count()) {
            $statuses = collect([]);
            $firstStatus = false;
            $defaults = [
                'backlog',
                'ready_to_do',
                'in_progress',
                'done',
            ];
            for ($i = 0; $i < count($defaults); $i++) {
                $status = TaskStatus::createNew();
                $status->name = trans('texts.' . $defaults[$i]);
                $status->sort_order = $i;
                $status->save();
                $statuses[] = $status;
                if (!$firstStatus) {
                    $firstStatus = $status;
                }
            }
            $i = 0;
            foreach ($tasks as $task) {
                $task->task_status_id = $firstStatus->id;
                $task->task_status_sort_order = $i++;
                $task->save();
            }
            // otherwise, check that the orders are correct
        } else {
            for ($i = 0; $i < $statuses->count(); $i++) {
                $status = $statuses[$i];
                if ($status->sort_order != $i) {
                    $status->sort_order = $i;
                    $status->save();
                }
            }

            $firstStatus = $statuses[0];
            $counts = [];
            foreach ($tasks as $task) {
                if (!$task->task_status || $task->task_status->trashed()) {
                    $task->task_status_id = $firstStatus->id;
                    $task->setRelation('task_status', $firstStatus);
                }
                if (!isset($counts[$task->task_status_id])) {
                    $counts[$task->task_status_id] = 0;
                }
                if ($task->task_status_sort_order != $counts[$task->task_status_id]) {
                    $task->task_status_sort_order = $counts[$task->task_status_id];
                }
                $counts[$task->task_status_id]++;
                if ($task->isDirty()) {
                    $task->save();
                }
            }
        }

        $data = [
            'showBreadcrumbs' => false,
            'title' => trans('texts.kanban'),
            'statuses' => $statuses,
            'tasks' => $tasks,
            'clients' => $clients,
            'projects' => $projects,
            'clientPublicId' => $clientPublicId,
            'client' => $clientPublicId ? Client::scope($clientPublicId)->first() : null,
            'projectPublicId' => $projectPublicId,
            'project' => $projectPublicId ? Project::scope($projectPublicId)->first() : null,
        ];

        return view('tasks.kanban', $data);
    }


    public function storeStatus()
    {
        $status = TaskStatus::createNew();
        $status->fill(request()->all());
        $status->save();

        return response()->json($status);
    }

    public function updateStatus($publicId)
    {
        $status = TaskStatus::scope($publicId)->firstOrFail();

        $origSortOrder = $status->sort_order;
        $newSortOrder = request('sort_order');

        if (request()->has('sort_order') && $newSortOrder != $origSortOrder) {
            TaskStatus::Scope()
                ->where('sort_order', '>', $origSortOrder)
                ->decrement('sort_order');

            TaskStatus::Scope()
                ->where('sort_order', '>=', $newSortOrder)
                ->increment('sort_order');
        }

        $status->fill(request()->all());
        $status->save();

        return response()->json($status);
    }

    public function deleteStatus($publicId)
    {
        $status = TaskStatus::scope($publicId)->firstOrFail();
        $status->delete();

        TaskStatus::Scope()
            ->where('sort_order', '>', $status->sort_order)
            ->decrement('sort_order');

        $firstStatus = TaskStatus::Scope()
            ->orderBy('sort_order')
            ->first();

        // Move linked tasks to the end of the first status
        if ($firstStatus) {
            $firstCount = $firstStatus->tasks->count();
            Task::Scope()
                ->where('task_status_id', '=', $status->id)
                ->increment('task_status_sort_order', $firstCount, [
                    'task_status_id' => $firstStatus->id
                ]);
        }

        return response()->json(['message' => RESULT_SUCCESS]);
    }

    public function updateTask($publicId)
    {
        $task = Task::scope($publicId)->firstOrFail();

        $origStatusId = $task->task_status_id;
        $origSortOrder = $task->task_status_sort_order;

        $newStatusId = TaskStatus::getPrivateId(request('task_status_id'));
        $newSortOrder = request('task_status_sort_order');

        Task::Scope()
            ->where('task_status_id', '=', $origStatusId)
            ->where('task_status_sort_order', '>', $origSortOrder)
            ->decrement('task_status_sort_order');

        Task::Scope()
            ->where('task_status_id', '=', $newStatusId)
            ->where('task_status_sort_order', '>=', $newSortOrder)
            ->increment('task_status_sort_order');

        $task->task_status_id = $newStatusId;
        $task->task_status_sort_order = $newSortOrder;
        $task->save();

        return response()->json($task);
    }

}
