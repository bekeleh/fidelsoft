<?php

namespace App\Ninja\Repositories;

use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceItemRepository extends BaseRepository
{
    private $model;

    public function __construct(InvoiceItem $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\InvoiceItem';
    }

    public function all()
    {
        return InvoiceItem::scope()->withTrashed()->where('is_deleted', false)->get();
    }

    public function findProduct($productPublicId = null)
    {
        if (!$productPublicId) {
            return null;
        }

        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('invoice_items.product_id', '=', $productId);

        return $query;
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('invoice_items')
            ->join('accounts', 'accounts.id', '=', 'invoice_items.account_id')
            ->leftJoin('products', 'products.id', '=', 'invoice_items.product_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('users', 'users.id', '=', 'invoice_items.user_id')
            ->where('invoice_items.invoice_item_type_id', '=', true)
            ->where('invoice_items.account_id', '=', $accountId)
//            ->where('invoice_items.deleted_at', '=', null)
            ->select(
                'invoice_items.id',
                'invoice_items.public_id',
                'invoice_items.name as invoice_item_name',
                'invoice_items.qty',
                'invoice_items.demand_qty',
                'invoice_items.cost',
                'invoice_items.discount',
                'invoice_items.is_deleted',
                'invoice_items.notes',
                'invoice_items.created_at',
                'invoice_items.updated_at',
                'invoice_items.deleted_at',
                'invoice_items.created_by',
                'invoice_items.updated_by',
                'invoice_items.deleted_by',
                'invoices.public_id as invoice_public_id',
                'invoices.invoice_number',
                'products.public_id as product_public_id',
                'products.product_key'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('invoice_items.name', 'like', '%' . $filter . '%')
                    ->orWhere('invoice_items.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('invoices.invoice_number', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_INVOICE_ITEM);

        return $query;
    }

    public function save($publicId, $data, $invoiceItem = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($invoiceItem) {
            $invoiceItem->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $invoiceItem = InvoiceItem::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $invoiceItem = InvoiceItem::createNew();
            $invoiceItem->created_by = Auth::user()->username;
        }
        $invoiceItem->fill($data);
        $invoiceItem->name = isset($data['name']) ? trim($data['name']) : '';
        $invoiceItem->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $invoiceItem->save();

        return $invoiceItem;
    }

    public function findPhonetically($invoiceItemName)
    {
        $invoiceItemNameMeta = metaphone($invoiceItemName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $invoiceItemId = 0;
        $invoiceItems = InvoiceItem::scope()->get();
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