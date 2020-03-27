<?php

namespace App\Ninja\Repositories;

use App\Events\SaleTypeWasCreated;
use App\Events\SaleTypeWasUpdated;
use App\Models\SaleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleTypeRepository extends BaseRepository
{
    private $model;

    public function __construct(SaleType $model)
    {
        $this->model = $model;
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function getClassName()
    {
        return 'App\Models\SaleType';
    }

    public function all()
    {
        return SaleType::scope()
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('sale_types')
            ->select('sale_types.*')
            ->where('sale_types.account_id', '=', $accountId);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('sale_types.name', 'like', '%' . $filter . '%')
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
            \Log::warning('Entity not set in sales_type repo save');
        } else {
            $saleType = SaleType::createNew();
            $saleType->created_by = Auth::user()->username;
        }

        $saleType->fill($data);
        $saleType->name = isset($data['name']) ? ucwords(trim($data['name'])) : '';
        $saleType->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $saleType->save();

        if ($publicId) {
            event(new SaleTypeWasUpdated($saleType, $data));
        } else {
            event(new SaleTypeWasCreated($saleType, $data));
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
