<?php

namespace App\Ninja\Repositories;

use App\Models\PurchaseInvoiceItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceItemRepository extends BaseRepository
{
    private $model;

    public function __construct(PurchaseInvoiceItem $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\PurchaseInvoiceItem';
    }

    public function all()
    {
        return PurchaseInvoiceItem::scope()->withTrashed()
            ->where('is_deleted', false)->get();
    }

    public function findProduct($productPublicId = null)
    {
        if (!$productPublicId) {
            return null;
        }

        $productId = Product::getPrivateId($productPublicId);

        $query = $this->find()->where('purchase_invoice_items.product_id', $productId);

        return $query;
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('purchase_invoice_items')
            ->leftJoin('accounts', 'accounts.id', '=', 'purchase_invoice_items.account_id')
            ->leftJoin('products', 'products.id', '=', 'purchase_invoice_items.product_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'purchase_invoice_items.invoice_id')
            ->leftJoin('users', 'users.id', '=', 'purchase_invoice_items.user_id')
            ->where('purchase_invoice_items.invoice_item_type_id', '=', true)
            ->where('purchase_invoice_items.account_id', '=', $accountId)
//            ->where('purchase_invoice_items.deleted_at', '=', null)
            ->select(
                'purchase_invoice_items.id',
                'purchase_invoice_items.public_id',
                'purchase_invoice_items.product_key as invoice_item_name',
                'purchase_invoice_items.qty',
                'purchase_invoice_items.demand_qty',
                'purchase_invoice_items.cost',
                'purchase_invoice_items.discount',
                'purchase_invoice_items.is_deleted',
                'purchase_invoice_items.notes',
                'purchase_invoice_items.created_at',
                'purchase_invoice_items.updated_at',
                'purchase_invoice_items.deleted_at',
                'purchase_invoice_items.created_by',
                'purchase_invoice_items.updated_by',
                'purchase_invoice_items.deleted_by',
                'invoices.public_id as invoice_public_id',
                'invoices.invoice_number',
                'products.public_id as product_public_id',
                'products.product_key'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('purchase_invoice_items.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('purchase_invoice_items.notes', 'like', '%' . $filter . '%')
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
            $invoiceItem = PurchaseInvoiceItem::scope($publicId)->withArchived()->firstOrFail();
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
        $invoiceItems = PurchaseInvoiceItem::scope()->get();
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