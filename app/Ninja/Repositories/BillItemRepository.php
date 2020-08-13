<?php

namespace App\Ninja\Repositories;

use App\Models\BillItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillItemRepository extends BaseRepository
{
    private $model;

    public function __construct(BillItem $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\BillItem';
    }

    public function all()
    {
        return BillItem::scope()->withTrashed()
            ->where('is_deleted', false)->get();
    }

    public function findProduct($productPublicId = null)
    {
        if (!$productPublicId) {
            return null;
        }

        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('Bill_items.product_id', $productId);

        return $query;
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('Bill_items')
            ->leftJoin('accounts', 'accounts.id', '=', 'Bill_items.account_id')
            ->leftJoin('products', 'products.id', '=', 'Bill_items.product_id')
            ->leftJoin('bills', 'bills.id', '=', 'Bill_items.invoice_id')
            ->leftJoin('users', 'users.id', '=', 'Bill_items.user_id')
            ->where('Bill_items.invoice_item_type_id', '=', true)
            ->where('Bill_items.account_id', '=', $accountId)
//            ->where('Bill_items.deleted_at', '=', null)
            ->select(
                'Bill_items.id',
                'Bill_items.public_id',
                'Bill_items.product_key as invoice_item_name',
                'Bill_items.qty',
                'Bill_items.demand_qty',
                'Bill_items.cost',
                'Bill_items.discount',
                'Bill_items.is_deleted',
                'Bill_items.notes',
                'Bill_items.created_at',
                'Bill_items.updated_at',
                'Bill_items.deleted_at',
                'Bill_items.created_by',
                'Bill_items.updated_by',
                'Bill_items.deleted_by',
                'bills.public_id as bill_public_id',
                'bills.bill_number',
                'products.public_id as product_public_id',
                'products.product_key'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('Bill_items.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('Bill_items.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('bills.bill_number', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_BILL_ITEM);

        return $query;
    }

    public function save($data, $invoiceItem = null)
    {
        $publicId = !empty($data['public_id']) ? $data['public_id'] : false;

        if ($invoiceItem) {
            $invoiceItem->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $invoiceItem = BillItem::scope($publicId)->withArchived()->firstOrFail();
        } else {
            //  can't be create new instance of invoice item
        }

        if ($invoiceItem) {
            $invoiceItem->notes = isset($data['notes']) ? trim($data['notes']) : null;
            $invoiceItem->save();
        }

        return $invoiceItem;
    }

    public function findPhonetically($invoiceItemName)
    {
        $invoiceItemNameMeta = metaphone($invoiceItemName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $invoiceItemId = 0;
        $invoiceItems = BillItem::scope()->get();
        if (!empty($invoiceItems)) {
            foreach ($invoiceItems as $invoiceItem) {
                if (!$invoiceItem->name) {
                    continue;
                }
                $map[$invoiceItem->id] = $invoiceItem;
                $similar = similar_text($invoiceItemNameMeta, metaphone($invoiceItem->name), $percent);
                if ($percent > $max) {
                    $invoiceItemId = $invoiceItem->id;
                    $max = $percent;
                }
            }
        }

        return ($invoiceItemId && isset($map[$invoiceItemId])) ? $map[$invoiceItemId] : null;
    }
}