<?php

namespace App\Ninja\Repositories;

use App\Models\Common\AccountGateway;
use Illuminate\Support\Facades\Auth;
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
        return 'App\Models\Common\AccountGateway';
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('account_gateways')
        ->leftJoin('gateways', 'gateways.id', '=', 'account_gateways.gateway_id')
        ->leftJoin('accounts', 'accounts.id', '=', 'account_gateways.account_id')
        ->where('account_gateways.account_id', '=', $accountId)
        // ->whereNull('account_gateways.deleted_at')
        ->select(
            'account_gateways.id',
            'account_gateways.public_id',
            'gateways.name as gateway_name',
            'gateways.public_id as gateway_public_id',
            'account_gateways.deleted_at',
            'account_gateways.gateway_id',
            'accounts.gateway_fee_enabled'
        );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('account_gateways.name', 'like', '%' . $filter . '%')
                ->orWhere('account_gateways.gateway_fee_enabled', 'like', '%' . $filter . '%')
                ->orWhere('account_gateways.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ACCOUNT_GATEWAY);

        return $query;
    }

    public function save($data, $accountGateway = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($accountGateway) {
            $accountGateway->updated_by = auth::user()->username;
        } elseif ($publicId) {
            $accountGateway = AccountGateway::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $accountGateway = AccountGateway::createNew();
            $accountGateway->created_by = auth::user()->username;
        }

        $accountGateway->fill($data);
        $accountGateway->name = isset($data['name']) ? trim($data['name']) : '';
        $accountGateway->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $accountGateway->save();

        return $accountGateway;
    }

    public function findPhonetically($accountGatewayName)
    {
        $accountGatewayNameMeta = metaphone($accountGatewayName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $accountGatewayId = 0;
        $accountGateways = AccountGateway::scope()->get();
        if (!empty($accountGateways)) {
            foreach ($accountGateways as $accountGateway) {
                if (!$accountGateway->name) {
                    continue;
                }
                $map[$accountGateway->id] = $accountGateway;
                $similar = similar_text($accountGatewayNameMeta, metaphone($accountGateway->name), $percent);
                if ($percent > $max) {
                    $accountGatewayId = $accountGateway->id;
                    $max = $percent;
                }
            }
        }

        return ($accountGatewayId && isset($map[$accountGatewayId])) ? $map[$accountGatewayId] : null;
    }
}
