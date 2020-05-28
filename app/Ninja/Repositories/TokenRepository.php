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

    public function find($userId = false)
    {
        $query = DB::table('account_tokens')
            ->where('account_tokens.user_id', '=', $userId)
            ->whereNull('account_tokens.deleted_at')
            ->select(
                'account_tokens.public_id',
                'account_tokens.name',
                'account_tokens.token',
                'account_tokens.public_id',
                'account_tokens.deleted_at'
            );

        return $query;
    }
}
