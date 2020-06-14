<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\ItemMovement;
use App\Models\ItemRequest;
use App\Models\Product;
use App\Models\Status;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

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
        return ItemRequest::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_requests')
            ->join('accounts', 'accounts.id', '=', 'item_requests.account_id')
            ->join('users', 'users.id', '=', 'item_requests.user_id')
            ->join('products', 'products.id', '=', 'item_requests.product_id')
            ->leftjoin('departments', 'departments.id', '=', 'item_requests.department_id')
            ->leftjoin('stores', 'stores.id', '=', 'item_requests.store_id')
            ->leftjoin('statuses', 'statuses.id', '=', 'item_requests.status_id')
            ->where('item_requests.account_id', '=', $accountId)
            //->where('item_requests.deleted_at', '=', null)
            ->select(
                'item_requests.id',
                'item_requests.public_id',
                'item_requests.user_id',
                'item_requests.product_id',
                'item_requests.department_id',
                'item_requests.store_id',
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
                'products.name as product_name',
                'products.public_id as product_public_id',
                'departments.name as department_name',
                'departments.public_id as department_public_id',
                'stores.name as store_name',
                'stores.public_id as store_public_id',
                'statuses.name as status_name',
                'statuses.public_id as status_public_id'
            );
        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('item_requests.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.name', 'like', '%' . $filter . '%')
                    ->orWhere('departments.name', 'like', '%' . $filter . '%')
                    ->orWhere('stores.name', 'like', '%' . $filter . '%')
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
            Log::warning('Entity not set in item request repo save');
        } else {
            $itemRequest = ItemRequest::createNew();
            $itemRequest->created_by = Auth::user()->username;
        }

        if (empty($data['status_id'])) {
            $itemRequest->status_id = Utils::getStatusId('pending');
        }

        $itemRequest->fill($data);
        $itemRequest->required_date = isset($data['required_date']) ? Utils::toSqlDate($data['required_date']) : Carbon::now();
        $itemRequest->save();

        return $itemRequest;
    }

    public function findProduct($productPublicId)
    {
        if (!$productPublicId) {
            return null;
        }

        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('item_requests.product_id', '=', $productId);

        return $query;
    }

    public function findDepartment($departmentPublicId)
    {
        if (!$departmentPublicId) {
            return null;
        }

        $productId = Product::getPrivateId($departmentPublicId);

        $query = $this->find()->where('item_requests.product_id', '=', $productId);

        return $query;
    }

    public function findStore($storePublicId)
    {
        if (!$storePublicId) {
            return null;
        }
        $storeId = Store::getPrivateId($storePublicId);

        $query = $this->find()->where('item_requests.store_id', '=', $storeId);

        return $query;
    }

    public function findStatus($statusPublicId)
    {
        if (!$statusPublicId) {
            return null;
        }
        $statusId = Status::getPrivateId($statusPublicId);

        $query = $this->find()->where('item_requests.store_id', '=', $statusId);

        return $query;
    }

    public function inventoryAdjustment($data, $itemRequest = null, $update = false)
    {
        if ($update) {
            $this->qoh = (int)$itemRequest->qty;
            if (!empty($data['qty'])) {
                if ((int)$data['qty'] > 0) {
                    $movable = ItemMovement::createNew();
                    $itemRequest = $this->itemTransfer->where('store_id', $itemRequest->current_store_id)->where('product_id', $itemRequest->product_id)->first();
                    $movable->qty = (int)$data['qty'];
                    $movable->qoh = ((int)$this->qoh) + ((int)$data['qty']);
                    $movable->notes = 'quantity adjustment';
                    $movable->updated_by = auth::user()->username;
                    $itemRequest->itemMovements()->save($movable);
                }
            }
        } else {
//           create new quantity
            if (!empty($data['qty'])) {
                if ((int)$data['qty'] > 0) {
                    $movable = ItemMovement::createNew();
                    $movable->qty = (int)$data['qty'];
                    $movable->qoh = (int)$data['qty'];
                    $movable->notes = 'quantity adjustment';
                    $movable->updated_by = auth::user()->username;
                    $itemRequest->itemMovements()->save($movable);
                }
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