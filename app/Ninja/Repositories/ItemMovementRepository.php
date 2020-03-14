<?php

namespace App\Ninja\Repositories;

use App\Models\ItemMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemMovementRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\ItemMovement';
    }

    public function all()
    {
        return ItemMovement::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }


    public function find($accountId, $filter = null)
    {
        $query = DB::table('item_movements')
            ->join('accounts', 'accounts.id', '=', 'item_movements.account_id')
            ->join('item_stores', function ($join) {
                $join->on('item_stores.id', '=', 'item_movements.movable_id')
                    ->join('products', 'products.id', '=', 'item_stores.product.id')
                    ->select('products.name as product_name')
                    ->join('stores', 'stores.id', '=', 'item_stores.store.id')
                    ->select('stores.name as item_name');
            })
            ->where('item_movements.account_id', '=', $accountId)
            //->where('item_movements.deleted_at', '=', null)
            ->select(
                'item_movements.id',
                'item_movements.public_id',
                'item_movements.movable_id',
                'item_movements.is_deleted',
                'item_movements.notes',
                'item_movements.created_at',
                'item_movements.updated_at',
                'item_movements.deleted_at',
                'item_movements.created_by',
                'item_movements.updated_by',
                'item_movements.deleted_by'
            );

        $this->applyFilters($query, ENTITY_ITEM_MOVEMENT);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('item_movements.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_movements.store_code', 'like', '%' . $filter . '%')
                    ->orWhere('item_movements.notes', 'like', '%' . $filter . '%')
                    ->orWhere('item_name.name', 'like', '%' . $filter . '%')
                    ->orWhere('store_name.name', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function save($data, $itemMovement = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemMovement) {
            $itemMovement->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $itemMovement = ItemMovement::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in store repo save');
        } else {
            $itemMovement = ItemMovement::createNew();
            $itemMovement->created_by = Auth::user()->username;
        }
        $itemMovement->fill($data);
        $itemMovement->notes = isset($data['notes']) ? trim($data['notes']) : '';
//      save the data
        $itemMovement->save();

        return $itemMovement;
    }

    public function findPhonetically($itemMovementName)
    {
        $itemMovementNameMeta = metaphone($itemMovementName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemMovementId = 0;
        $itemMovements = ItemMovement::scope()->get();
        if (!empty($itemMovements)) {
            foreach ($itemMovements as $itemMovement) {
                if (!$itemMovement->name) {
                    continue;
                }
                $map[$itemMovement->id] = $itemMovement;
                $similar = similar_text($itemMovementNameMeta, metaphone($itemMovement->name), $percent);
                if ($percent > $max) {
                    $itemMovementId = $itemMovement->id;
                    $max = $percent;
                }
            }
        }

        return ($itemMovementId && isset($map[$itemMovementId])) ? $map[$itemMovementId] : null;
    }
}