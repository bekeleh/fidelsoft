<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;

class TimeTrackerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $account = $user->account;

        if (!$account->hasFeature(FEATURE_TASKS)) {
            return trans('texts.tasks_not_enabled');
        }

        $data = [
            'title' => trans('texts.time_tracker'),
            'tasks' => Task::Scope()->with('project', 'client.contacts', 'task_status')->whereNull('invoice_id')->get(),
            'clients' => Client::Scope()->with('contacts')->orderBy('name')->get(),
            'projects' => Project::Scope()->with('client.contacts')->orderBy('name')->get(),
            'statuses' => TaskStatus::Scope()->orderBy('sort_order')->get(),
            'account' => $account,
        ];

        return view('tasks.time_tracker', $data);
    }
}
