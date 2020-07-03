<?php

namespace App\Ninja\Repositories;

use App\Models\Token;
use Illuminate\Support\Facades\DB;

class TokenRepository extends BaseRepository
{
    private $model;

    public function __construct(Token $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\AccountToken';
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('account_tokens')
        ->leftJoin('accounts', 'accounts.id', '=', 'account_tokens.account_id')
        ->leftJoin('users', 'users.id', '=', 'account_tokens.user_id')
        ->where('account_tokens.user_id', '=', $accountId)
//            ->whereNull('account_tokens.deleted_at')
        ->select(
            'account_tokens.public_id',
            'account_tokens.name',
            'account_tokens.token',
            'account_tokens.is_deleted',
            'account_tokens.notes',
            'account_tokens.public_id',
            'account_tokens.created_at',
            'account_tokens.updated_at',
            'account_tokens.deleted_at',
            'account_tokens.created_by',
            'account_tokens.updated_by',
            'account_tokens.deleted_by'
        );

        if (!$filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('account_tokens.name', 'like', '$' . $filter . '%')
                ->orwhere('account_tokens.token', 'like', '$' . $filter . '%')
                ->orwhere('account_tokens.notes', 'like', '$' . $filter . '%')
                ->orwhere('account_tokens.created_by', 'like', '$' . $filter . '%');
            });
        }

        return $query;
    }
}
