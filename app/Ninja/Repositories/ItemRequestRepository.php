<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\ItemMovement;
use App\Models\ItemRequest;
use App\Models\Product;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemRequestRepository extends BaseRepository
{
    private $model;

    public function __construct(ItemRequest $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ItemRequest';
    }

    public function all()
    {
        return ItemRequest::scope()->withTrashed()->where('is_deleted', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_requests')
            ->LeftJoin('accounts', 'accounts.id', '=', 'item_requests.account_id')
            ->LeftJoin('users', 'users.id', '=', 'item_requests.user_id')
            ->LeftJoin('products', 'products.id', '=', 'item_requests.product_id')
            ->leftjoin('departments', 'departments.id', '=', 'item_requests.department_id')
            ->leftjoin('warehouses', 'warehouses.id', '=', 'item_requests.warehouse_id')
            ->leftjoin('statuses', 'statuses.id', '=', 'item_requests.status_id')
            ->where('item_requests.account_id', '=', $accountId)
            //->where('item_requests.deleted_at', '=', null)
            ->select(
                'item_requests.id',
                'item_requests.public_id',
                'item_requests.user_id',
                'item_requests.product_id',
                'item_requests.department_id',
                'item_requests.warehouse_id',
                'item_requests.status_id',
                'item_requests.qty',
                'item_requests.delivered_qty',
                'item_requests.is_deleted',
                'item_requests.notes',
                'item_requests.required_date',
                'item_requests.dispatch_date',
                'item_requests.created_at',
                'item_requests.updated_at',
                'item_requests.deleted_at',
                'item_requests.created_by',
                'item_requests.updated_by',
                'item_requests.deleted_by',
                'products.product_key',
                'products.public_id as product_public_id',
                'departments.name as department_name',
                'departments.public_id as department_public_id',
                'warehouses.name as warehouse_name',
                'warehouses.public_id as store_public_id',
                'statuses.name as status_name',
                'statuses.public_id as status_public_id'
            );
        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('item_requests.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('departments.name', 'like', '%' . $filter . '%')
                    ->orWhere('warehouses.name', 'like', '%' . $filter . '%')
                    ->orWhere('statuses.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_requests.created_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_requests.updated_by', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_REQUEST);

        return $query;
    }

    public function save($data, $itemRequest = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemRequest) {
            $itemRequest->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $itemRequest = ItemRequest::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $itemRequest = ItemRequest::createNew();
            $itemRequest->created_by = Auth::user()->username;
        }

        if (!isset($data['status_id'])) {
            $itemRequest->status_id = Utils::getStatusId('pending');
        }

        $itemRequest->fill($data);
        $itemRequest->required_date = isset($data['required_date']) ? Utils::toSqlDate($data['required_date']) : Carbon::now();
        $itemRequest->save();

        return $itemRequest;
    }

    public function findProduct($productPublicId)
    {
        if (empty($productPublicId)) {
            return;
        }

        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('item_requests.product_id', '=', $productId);

        return $query;
    }

    public function findDepartment($departmentPublicId)
    {
        if (empty($departmentPublicId)) {
            return;
        }

        $productId = Product::getPrivateId($departmentPublicId);

        $query = $this->find()->where('item_requests.product_id', '=', $productId);

        return $query;
    }

    public function findWarehouse($warehousePublicId)
    {
        if (empty($warehousePublicId)) {
            return;
        }
        $warehouseId = Warehouse::getPrivateId($warehousePublicId);

        $query = $this->find()->where('item_requests.warehouse_id', '=', $warehouseId);

        return $query;
    }

    // public function findStatus($statusPublicId)
    // {
    //     if (empty($statusPublicId)) {
    //         return;
    //     }
    //     $statusId = Status::getPrivateId($statusPublicId);

    //     $query = $this->find()->where('item_requests.warehouse_id', '=', $statusId);

    //     return $query;
    // }

    public function stockAdjustment($data, $itemRequest = null, $update = false)
    {
        if (empty($data['qty']) || empty($itemRequest)) {
            return;
        }

        $newQty = isset($data['qty']) ? Utils::parseFloat($data['qty']) : 0;
        $qoh = isset($itemRequest->qty) ? Utils::parseFloat($itemRequest->qty) : 0;

        if ($update) {
            if ($newQty > 0) {
                $movable = ItemMovement::createNew();
                $itemRequest = $this->itemTransfer
                    ->where('warehouse_id', $itemRequest->current_warehouse_id)
                    ->where('product_id', $itemRequest->product_id)->first();
                $movable->qty = $newQty;
                $movable->qoh = ($qoh + $newQty);
                $movable->notes = 'stock request';
                $movable->updated_by = auth::user()->username;
                $itemRequest->stockMovements()->save($movable);
            }
        } else {
            if ($newQty > 0) {
                $movable = ItemMovement::createNew();
                $movable->qty = $newQty;
                $movable->qoh = $newQty;
                $movable->notes = 'stock request';
                $movable->updated_by = auth::user()->username;
                $itemRequest->stockMovements()->save($movable);
            }
        }
    }

    public function findPhonetically($itemRequestName)
    {
        $itemRequestNameMeta = metaphone($itemRequestName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemRequestId = 0;
        $itemRequests = ItemRequest::scope()->get();
        if (!empty($itemRequests)) {
            foreach ($itemRequests as $itemRequest) {
                if (!$itemRequest->bin) {
                    continue;
                }
                $map[$itemRequest->id] = $itemRequest;
                $similar = similar_text($itemRequestNameMeta, metaphone($itemRequest->bin), $percent);
                if ($percent > $max) {
                    $itemRequestId = $itemRequest->id;
                    $max = $percent;
                }
            }
        }

        return ($itemRequestId && isset($map[$itemRequestId])) ? $map[$itemRequestId] : null;
    }
}