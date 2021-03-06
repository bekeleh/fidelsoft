<?php

namespace App\Ninja\Repositories;

use App\Models\ProposalTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProposalTemplateRepository extends BaseRepository
{
    private $model;

    public function __construct(ProposalTemplate $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ProposalTemplate';
    }

    public function all()
    {
        return ProposalTemplate::scope()->get();
    }

    public function find($account = false, $filter = null, $userId = false)
    {
        $query = DB::table('proposal_templates')
        ->leftJoin('accounts', 'accounts.id', '=', 'proposal_templates.account_id')
        ->leftJoin('users', 'users.id', '=', 'proposal_templates.user_id')
        ->where('proposal_templates.account_id', '=', Auth::user()->account_id)
        ->select(
            'proposal_templates.name',
            'proposal_templates.public_id',
            'proposal_templates.user_id',
            'proposal_templates.deleted_at',
            'proposal_templates.is_deleted',
            'proposal_templates.html as content',
            'proposal_templates.private_notes',
            'proposal_templates.created_at',
            'proposal_templates.updated_at',
            'proposal_templates.deleted_at',
            'proposal_templates.created_by',
            'proposal_templates.updated_by',
            'proposal_templates.deleted_by'
        );

        $this->applyFilters($query, ENTITY_PROPOSAL_TEMPLATE);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('clients.name', 'like', '%' . $filter . '%')
                ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                ->orWhere('contacts.email', 'like', '%' . $filter . '%')
                ->orWhere('proposal_templates.name', 'like', '%' . $filter . '%');
            });
        }

        if ($userId) {
            $query->where('proposal_templates.user_id', '=', $userId);
        }

        return $query;
    }

    public function save($input, $proposal = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if (!$proposal) {
            $proposal = ProposalTemplate::createNew();
        }

        $proposal->fill($input);
        $proposal->save();

        return $proposal;
    }
}
