<?php

namespace App\Ninja\Repositories;

use App\Events\ClientTypeWasCreated;
use App\Events\ClientTypeWasUpdated;
use App\Models\ClientType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientTypeRepository extends BaseRepository
{
    private $model;

    public function __construct(ClientType $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ClientType';
    }

    public function all()
    {
        return ClientType::scope()
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('client_types')
            ->leftJoin('accounts', 'accounts.id', '=', 'client_types.account_id')
            ->leftJoin('users', 'users.id', '=', 'client_types.user_id')
            ->where('client_types.account_id', '=', $accountId)
            //  ->whereNull('client_types.deleted_at')
            ->select(
                'client_types.id',
                'client_types.public_id',
                'client_types.name as client_type_name',
                'client_types.is_deleted',
                'client_types.notes',
                'client_types.created_at',
                'client_types.updated_at',
                'client_types.deleted_at',
                'client_types.created_by',
                'client_types.updated_by',
                'client_types.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('client_types.name', 'like', '%' . $filter . '%')
                    ->orWhere('client_types.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_CLIENT_TYPE);

        return $query;
    }

    public function save($data, $clientType = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($clientType) {
            $clientType->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $clientType = ClientType::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $clientType = ClientType::createNew();
            $clientType->created_by = Auth::user()->username;
        }

        $clientType->fill($data);
        $clientType->name = isset($data['name']) ? trim($data['name']) : '';
        $clientType->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $clientType->save();

        if ($publicId) {
            event(new ClientTypeWasUpdated($clientType));
        } else {
            event(new ClientTypeWasCreated($clientType));
        }

        return $clientType;
    }

    public function findPhonetically($clientTypeName)
    {
        $clientTypeNameMeta = metaphone($clientTypeName);

        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $clientTypeId = 0;

        $clientTypes = ClientType::scope()->get();

        foreach ($clientTypes as $clientType) {
            if (!$clientType->name) {
                continue;
            }

            $map[$clientType->id] = $clientType;
            $similar = similar_text($clientTypeNameMeta, metaphone($clientType->name), $percent);

            if ($percent > $max) {
                $clientTypeId = $clientType->id;
                $max = $percent;
            }
        }

        return ($clientTypeId && isset($map[$clientTypeId])) ? $map[$clientTypeId] : null;
    }
}
