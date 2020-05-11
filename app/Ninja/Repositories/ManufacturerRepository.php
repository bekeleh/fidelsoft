<?php

namespace App\Ninja\Repositories;

use App\Models\Manufacturer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Events\ManufacturerWasCreated;
use App\Events\ManufacturerWasUpdated;

class ManufacturerRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'Modules\Manufacturer\Models\Manufacturer';
    }

    public function all()
    {
        return Manufacturer::scope()
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('manufacturers')
            ->where('manufacturers.account_id', '=', $accountId)
//            ->where('manufacturers.deleted_at', '=', null)
            ->select(
                'manufacturers.name as manufacturer_name',
                'manufacturers.notes',
                'manufacturers.public_id',
                'manufacturers.deleted_at',
                'manufacturers.created_at',
                'manufacturers.updated_at',
                'manufacturers.is_deleted',
                'manufacturers.user_id',
                'manufacturers.created_by',
                'manufacturers.updated_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('manufacturers.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_MANUFACTURER);

        return $query;
    }

    public function save($data, $manufacturer = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($manufacturer) {
            $manufacturer->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $manufacturer = Manufacturer::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in manufacturer repo save');
        } else {
            $manufacturer = Manufacturer::createNew();
            $manufacturer->created_by = Auth::user()->username;
        }

        $manufacturer->fill($data);
        $manufacturer->save();

        if (!$publicId || $publicId == '-1') {
            event(new ManufacturerWasCreated($manufacturer));
        } else {
            event(new ManufacturerWasUpdated($manufacturer));
        }

        return $manufacturer;
    }
}