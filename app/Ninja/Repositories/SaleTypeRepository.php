<?php

namespace App\Ninja\Repositories;

use App\Events\Client\SaleTypeWasCreatedEvent;
use App\Events\Client\SaleTypeWasUpdatedEvent;
use App\Models\Setting\SaleType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleTypeRepository extends BaseRepository
{
    private $model;

    public function __construct(SaleType $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Setting\SaleType';
    }

    public function all()
    {
        return SaleType::scope()
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('sale_types')
            ->leftJoin('accounts', 'accounts.id', '=', 'sale_types.account_id')
            ->leftJoin('users', 'users.id', '=', 'sale_types.user_id')
            ->where('sale_types.account_id', '=', $accountId)
            //->where('sale_types.deleted_at', '=', null)
            ->select(
                'sale_types.id',
                'sale_types.public_id',
                'sale_types.name as sale_type_name',
                'sale_types.is_deleted',
                'sale_types.notes',
                'sale_types.created_at',
                'sale_types.updated_at',
                'sale_types.deleted_at',
                'sale_types.created_by',
                'sale_types.updated_by',
                'sale_types.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('sale_types.sale_type_name', 'like', '%' . $filter . '%')
                    ->orWhere('sale_types.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_SALE_TYPE);

        return $query;
    }

    public function save($data, $saleType = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($saleType) {
            $saleType->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $saleType = SaleType::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $saleType = SaleType::createNew();
            $saleType->created_by = Auth::user()->username;
        }

        $saleType->fill($data);
        $saleType->name = isset($data['name']) ? trim($data['name']) : '';
        $saleType->notes = isset($data['notes']) ? trim($data['notes']) : '';

        $saleType->save();

        if ($publicId) {
            event(new SaleTypeWasUpdatedEvent($saleType));
        } else {
            event(new SaleTypeWasCreatedEvent($saleType));
        }

        return $saleType;
    }

    public function findPhonetically($saleTypeName)
    {
        $saleTypeNameMeta = metaphone($saleTypeName);

        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $saleTypeId = 0;

        $saleTypes = SaleType::scope()->get();

        foreach ($saleTypes as $saleType) {
            if (!$saleType->name) {
                continue;
            }

            $map[$saleType->id] = $saleType;
            $similar = similar_text($saleTypeNameMeta, metaphone($saleType->name), $percent);

            if ($percent > $max) {
                $saleTypeId = $saleType->id;
                $max = $percent;
            }
        }

        return ($saleTypeId && isset($map[$saleTypeId])) ? $map[$saleTypeId] : null;
    }
}
