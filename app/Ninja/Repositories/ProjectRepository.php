<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectRepository extends BaseRepository
{
    private $model;

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Project';
    }

    public function all()
    {
        return Project::scope()->get();
    }

    public function find($filter = false, $userId = false)
    {
        $query = DB::table('projects')
        ->leftJoin('accounts', 'accounts.id', '=', 'projects.account_id')
        ->leftJoin('users', 'users.id', '=', 'projects.user_id')
        ->leftJoin('clients', 'clients.id', '=', 'projects.client_id')
        ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
        ->where('projects.account_id', '=', Auth::user()->account_id)
        ->where('contacts.deleted_at', '=', null)
        ->where('clients.deleted_at', '=', null)
            ->where(function ($query) { // handle when client isn't set
                $query->where('contacts.is_primary', '=', true)
                ->orWhere('contacts.is_primary', '=', null);
            })
            ->select(
                'projects.name as project',
                'projects.public_id',
                'projects.user_id',
                'projects.deleted_at',
                'projects.task_rate',
                'projects.is_deleted',
                'projects.due_date',
                'projects.budgeted_hours',
                'projects.private_notes',
                'projects.created_at',
                'projects.updated_at',
                'projects.deleted_at',
                'projects.created_by',
                'projects.updated_by',
                'projects.deleted_by',
                DB::raw("COALESCE(NULLIF(clients.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client_name"),
                'clients.user_id as client_user_id',
                'clients.public_id as client_public_id'
            );

            $this->applyFilters($query, ENTITY_PROJECT);

            if ($filter) {
                $query->where(function ($query) use ($filter) {
                    $query->where('clients.name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('projects.name', 'like', '%' . $filter . '%');
                });
            }

            if ($userId) {
                $query->where('projects.user_id', '=', $userId);
            }

            return $query;
        }

        public function save($input, $project = false)
        {
            $publicId = isset($data['public_id']) ? $data['public_id'] : false;

            if (!$project) {
                $project = Project::createNew();
                $project['client_id'] = $input['client_id'];
            }

            $project->fill($input);

            if (isset($input['due_date'])) {
                $project->due_date = Utils::toSqlDate($input['due_date']);
            }

            $project->save();

            return $project;
        }
    }
