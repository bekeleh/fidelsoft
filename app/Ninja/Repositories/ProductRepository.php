<?php

namespace App\Ninja\Repositories;

use App\Models\Product;
use App\Events\ProductWasCreated;
use App\Events\ProductWasUpdated;
use Utils;
use DB;

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
            ->where('products.account_id', '=', $accountId)
            ->select('products.*');

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('products.product_key', 'like', '%' . $filter . '%')
                    ->orWhere('products.notes', 'like', '%' . $filter . '%')
                    ->orWhere('products.custom_value1', 'like', '%' . $filter . '%')
                    ->orWhere('products.custom_value2', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_PRODUCT);

        return $query;
    }

    public function save($data, $product = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($product) {
            // do nothing
        } elseif ($publicId) {
            $product = Product::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in product repo save');
        } else {
            $product = Product::createNew();
        }

        $product->fill($data);
        $product->product_key = isset($data['product_key']) ? trim($data['product_key']) : '';
        $product->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $product->cost = isset($data['cost']) ? Utils::parseFloat($data['cost']) : 0;
        $product->qty = isset($data['qty']) ? Utils::parseFloat($data['qty']) : 1;
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
            if (!$product->product_key) {
                continue;
            }

            $map[$product->id] = $product;
            $similar = similar_text($productNameMeta, metaphone($product->product_key), $percent);

            if ($percent > $max) {
                $productId = $product->id;
                $max = $percent;
            }
        }

        return ($productId && isset($map[$productId])) ? $map[$productId] : null;
    }
}
