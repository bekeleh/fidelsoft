<?php

namespace App\Ninja\Repositories;

use App\Events\ItemPriceWasCreated;
use App\Events\ItemPriceWasUpdated;
use App\Models\ClientType;
use App\Models\ItemPrice;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemPriceRepository extends BaseRepository
{
    private $model;

    public function __construct(ItemPrice $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ItemPrice';
    }

    public function all()
    {
        return ItemPrice::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_prices')
            ->leftJoin('accounts', 'accounts.id', '=', 'item_prices.account_id')
            ->leftJoin('client_types', 'client_types.id', '=', 'item_prices.client_type_id')
            ->leftJoin('products', 'products.id', '=', 'item_prices.product_id')
            ->leftJoin('item_brands', 'item_brands.id', '=', 'products.item_brand_id')
            ->leftJoin('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->where('item_prices.account_id', '=', $accountId)
            //->where('item_prices.deleted_at', '=', null)
            ->select(
                'item_prices.id',
                'item_prices.public_id',
                'item_prices.product_id',
                'item_prices.client_type_id',
                'item_prices.unit_price',
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
                'client_types.public_id as client_type_public_id',
                'client_types.name as client_type_name',
                'products.public_id as product_public_id',
                'products.product_key as product_name',
                'products.cost',
                'item_brands.public_id as item_brand_public_id',
                'item_brands.name as item_brand_name',
                'item_categories.public_id as item_category_public_id',
                'item_categories.name as item_category_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('item_prices.created_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_prices.updated_by', 'like', '%' . $filter . '%')
                    ->orWhere('item_prices.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('item_brands.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_categories.name', 'like', '%' . $filter . '%')
                    ->orWhere('client_types.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_PRICE);

        return $query;
    }

    public function findItem($itemPublicId)
    {
        $itemId = Product::getPrivateId($itemPublicId);

        $query = $this->find()->where('item_prices.product_id', '=', $itemId);

        return $query;
    }

    public function findClientType($clientTypePublicId)
    {
        $clientTypeId = ClientType::getPrivateId($clientTypePublicId);

        $query = $this->find()->where('item_prices.client_type_id', '=', $clientTypeId);

        return $query;
    }

    public function save($data, $itemPrice = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemPrice) {
            $itemPrice->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $itemPrice = ItemPrice::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $itemPrice = ItemPrice::createNew();
            $itemPrice->created_by = Auth::user()->username;
        }

        $itemPrice->fill($data);

        $itemPrice->save();

        if ($publicId) {
            event(new ItemPriceWasUpdated($itemPrice));
        } else {
            event(new ItemPriceWasCreated($itemPrice));
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