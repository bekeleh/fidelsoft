<?php

namespace App\Ninja\Repositories;

use App\Events\ItemPriceWasCreated;
use App\Events\ItemPriceWasUpdated;
use App\Models\Product;
use App\Models\SaleType;
use App\Models\ItemPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ItemPriceRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\ItemPrice';
    }

    public function all()
    {
        return ItemPrice::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('item_prices')
            ->join('accounts', 'accounts.id', '=', 'item_prices.account_id')
            ->join('sale_types', 'sale_types.id', '=', 'item_prices.sale_type_id')
            ->join('products', 'products.id', '=', 'item_prices.product_id')
            ->join('item_categories', 'item_categories.id', '=', 'products.item_category_id')
            ->where('item_prices.account_id', '=', $accountId)
            //->where('item_prices.deleted_at', '=', null)
            ->select(
                'item_prices.id',
                'item_prices.public_id',
                'item_prices.product_id',
                'item_prices.sale_type_id',
                'item_prices.item_price',
                'item_prices.start_date',
                'item_prices.end_date',
                'item_prices.is_deleted',
                'item_prices.notes',
                'item_prices.created_at',
                'item_prices.updated_at',
                'item_prices.deleted_at',
                'item_prices.created_by',
                'item_prices.updated_by',
                'item_prices.deleted_by',
                'sale_types.name as sale_type_name',
                'products.name as item_name',
                'products.item_cost',
                'item_categories.name as item_category_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('item_prices.created_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_prices.updated_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_prices.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_categories.name', 'like', '%' . $filter . '%')
                    ->orWhere('sale_types.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_PRICE);

        return $query;
    }

    public function findItem($itemPublicId)
    {
        $itemId = Product::getPrivateId($itemPublicId);

        $query = $this->find()->where('products.product_id', '=', $itemId);

        return $query;
    }

    public function findSaleType($saleTypePublicId)
    {
        $saleTypeId = SaleType::getPrivateId($saleTypePublicId);

        $query = $this->find()->where('sale_types.sale_type_id', '=', $saleTypeId);

        return $query;
    }

    public function save($data, $itemPrice = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemPrice) {
            $itemPrice->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $itemPrice = ItemPrice::scope($publicId)->withArchived()->firstOrFail();
            Log::warning('Entity not set in price repo save');
        } else {
            $itemPrice = ItemPrice::createNew();
            $itemPrice->created_by = Auth::user()->username;
        }

        $itemPrice->fill($data);

        $itemPrice->save();

        if ($publicId) {
            event(new ItemPriceWasUpdated($itemPrice, $data));
        } else {
            event(new ItemPriceWasCreated($itemPrice, $data));
        }
        return $itemPrice;
    }

    public function findPhonetically($itemPriceName)
    {
        $itemPriceNameMeta = metaphone($itemPriceName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemPriceId = 0;
        $itemPrices = ItemPrice::scope()->get();
        if (!empty($itemPrices)) {
            foreach ($itemPrices as $itemPrice) {
                if (!$itemPrice->name) {
                    continue;
                }
                $map[$itemPrice->id] = $itemPrice;
                $similar = similar_text($itemPriceNameMeta, metaphone($itemPrice->name), $percent);
                if ($percent > $max) {
                    $itemPriceId = $itemPrice->id;
                    $max = $percent;
                }
            }
        }

        return ($itemPriceId && isset($map[$itemPriceId])) ? $map[$itemPriceId] : null;
    }
}