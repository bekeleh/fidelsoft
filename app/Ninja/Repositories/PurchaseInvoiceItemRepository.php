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

        $query = $this->find()->where('BILL_items.product_id', $productId);

        return $query;
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('BILL_items')
            ->leftJoin('accounts', 'accounts.id', '=', 'BILL_items.account_id')
            ->leftJoin('products', 'products.id', '=', 'BILL_items.product_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'BILL_items.invoice_id')
            ->leftJoin('users', 'users.id', '=', 'BILL_items.user_id')
            ->where('BILL_items.invoice_item_type_id', '=', true)
            ->where('BILL_items.account_id', '=', $accountId)
//            ->where('BILL_items.deleted_at', '=', null)
            ->select(
                'BILL_items.id',
                'BILL_items.public_id',
                'BILL_items.product_key as invoice_item_name',
                'BILL_items.qty',
                'BILL_items.demand_qty',
                'BILL_items.cost',
                'BILL_items.discount',
                'BILL_items.is_deleted',
                'BILL_items.notes',
                'BILL_items.created_at',
                'BILL_items.updated_at',
                'BILL_items.deleted_at',
                'BILL_items.created_by',
                'BILL_items.updated_by',
                'BILL_items.deleted_by',
                'invoices.public_id as invoice_public_id',
                'invoices.invoice_number',
                'products.public_id as product_public_id',
                'products.product_key'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('BILL_items.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('BILL_items.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('invoices.invoice_number', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_INVOICE_ITEM);

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