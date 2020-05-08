<?php

namespace App\Ninja\Repositories;

use App\Models\AccountGateway;
use Illuminate\Support\Facades\DB;

class AccountGatewayRepository extends BaseRepository
{
    private $model;

    public function __construct(AccountGateway $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\AccountGateway';
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('account_gateways')
            ->join('gateways', 'gateways.id', '=', 'account_gateways.gateway_id')
            ->join('accounts', 'accounts.id', '=', 'account_gateways.account_id')
            ->where('account_gateways.account_id', '=', $accountId)
            ->whereNull('account_gateways.deleted_at');

        return $query->select(
            'account_gateways.id',
            'account_gateways.public_id',
            'gateways.name as gateway_name',
            'gateways.public_id as gateway_public_id',
            'account_gateways.deleted_at',
            'account_gateways.gateway_id',
            'accounts.gateway_fee_enabled');
    }
}
