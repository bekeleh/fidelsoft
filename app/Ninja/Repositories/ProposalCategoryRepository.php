<?php

namespace App\Ninja\Repositories;

use App\Models\ProposalCategory;
use App\Models\Proposal;
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
        ->leftJoin('accounts', 'accounts.id', '=', 'proposal_categories.account_id')
        ->leftJoin('users', 'users.id', '=', 'proposal_categories.user_id')
        ->where('proposal_categories.account_id', '=', Auth::user()->account_id)
        ->select(
            'proposal_categories.name',
            'proposal_categories.public_id',
            'proposal_categories.user_id',
            'proposal_categories.is_deleted',
            'proposal_categories.created_at',
            'proposal_categories.updated_at',
            'proposal_categories.deleted_at',
            'proposal_categories.created_by',
            'proposal_categories.updated_by',
            'proposal_categories.deleted_by'
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

        if($proposal){
            $proposal->updated_by = auth::user()->username;
        }elseif($publicId){
            $proposal = ProposalCategory::scope($publicId)->withArchived()->findOrFail();
        }else {
            $proposal = ProposalCategory::createNew();
            $proposal->created_by = auth::user()->username;
        }

        $proposal->fill($input);
        $proposal->save();

        return $proposal;
    }
}
