<?php

namespace App\Ninja\Repositories;

use App\Events\ProductWasCreated;
use App\Events\ProductWasUpdated;
use App\Libraries\Utils;
use App\Models\ItemCategory;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\Product';
    }

    public function all()
    {
        return Product::scope()
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->get();
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('products')
            ->join('accounts', 'accounts.id', '=', 'products.account_id')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->join('item_categories', 'item_categories.id', '=', 'products.item_category_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->where('products.account_id', '=', $accountId)
            //->where('products.deleted_at', '=', null)
            ->select(
                'products.id',
                'products.public_id',
                'products.name as item_name',
                'products.barcode',
                'products.item_tag',
                'products.item_cost',
                'products.tax_name1',
                'products.tax_name2',
                'products.tax_rate1',
                'products.tax_rate2',
                'products.is_deleted',
                'products.notes',
                'products.notes',
                'products.created_at',
                'products.updated_at',
                'products.deleted_at',
                'products.created_by',
                'products.updated_by',
                'products.deleted_by',
                'item_categories.name as item_category_name',
                'units.name as unit_name'
            );
        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('products.name', 'like', '%' . $filter . '%')
                    ->orWhere('products.barcode', 'like', '%' . $filter . '%')
                    ->orWhere('products.item_tag', 'like', '%' . $filter . '%')
                    ->orWhere('products.notes', 'like', '%' . $filter . '%')
                    ->orWhere('item_categories.name', 'like', '%' . $filter . '%')
                    ->orWhere('units.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_PRODUCT);

        return $query;
    }

    public function findItemCategory($itemCategoryPublicId)
    {
        $itemCategoryId = ItemCategory::getPrivateId($itemCategoryPublicId);

        $query = $this->find()->where('item_categories.item_category_id', '=', $itemCategoryId);

        return $query;
    }

    public function findUnit($unitPublicId)
    {
        $unitId = Unit::getPrivateId($unitPublicId);

        $query = $this->find()->where('units.unit_id', '=', $unitId);

        return $query;
    }

    public function save($data, $product = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($product) {
            $product->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $product = Product::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in product repo save');
        } else {
            $product = Product::createNew();
            $product->created_by = auth::user()->username;
        }

        $product->fill($data);
        $product->name = isset($data['name']) ? ucwords(trim($data['name'])) : '';
        $product->barcode = isset($data['barcode']) ? ucwords(trim($data['barcode'])) : '';
        $product->item_tag = isset($data['item_tag']) ? ucwords(trim($data['item_tag'])) : '';
        $product->item_category_id = isset($data['item_category_id']) ? trim($data['item_category_id']) : '';
        $product->unit_id = isset($data['unit_id']) ? trim($data['unit_id']) : '';
        $product->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $product->item_cost = isset($data['item_cost']) ? Utils::parseFloat($data['item_cost']) : 0;
        $product->save();

        if ($publicId) {
            event(new ProductWasUpdated($product, $data));
        } else {
            event(new ProductWasCreated($product, $data));
        }
        return $product;
    }

    public function findPhonetically($productName)
    {
        $productNameMeta = metaphone($productName);

        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $productId = 0;

        $products = Product::scope()->get();

        foreach ($products as $product) {
            if (!$product->name) {
                continue;
            }

            $map[$product->id] = $product;
            $similar = similar_text($productNameMeta, metaphone($product->name), $percent);

            if ($percent > $max) {
                $productId = $product->id;
                $max = $percent;
            }
        }

        return ($productId && isset($map[$productId])) ? $map[$productId] : null;
    }
}
