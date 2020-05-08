<?php

namespace App\Ninja\Repositories;

use App\Models\ProposalCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProposalCategoryRepository extends BaseRepository
{
    private $model;

    public function __construct(ProposalCategory $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ProposalCategory';
    }

    public function all()
    {
        return ProposalCategory::scope()->get();
    }

    public function find($account = false, $filter = null, $userId = false)
    {
        $query = DB::table('proposal_categories')
            ->where('proposal_categories.account_id', '=', Auth::user()->account_id)
            ->select(
                'proposal_categories.name',
                'proposal_categories.public_id',
                'proposal_categories.user_id',
                'proposal_categories.deleted_at',
                'proposal_categories.is_deleted'
            );

        $this->applyFilters($query, ENTITY_PROPOSAL_CATEGORY);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('proposal_categories.name', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function save($input, $proposal = false)
    {
        $publicId = isset($input['public_id']) ? $input['public_id'] : false;

        if (!$proposal) {
            $proposal = ProposalCategory::createNew();
        }

        $proposal->fill($input);
        $proposal->save();

        return $proposal;
    }
}
