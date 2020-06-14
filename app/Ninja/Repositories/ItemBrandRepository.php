<?php

namespace App\Ninja\Repositories;

use App\Models\ItemBrand;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemBrandRepository extends BaseRepository
{
    private $model;

    public function __construct(ItemBrand $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ItemBrand';
    }

    public function all()
    {
        return ItemBrand::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }


    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_brands')
            ->join('accounts', 'accounts.id', '=', 'item_brands.account_id')
            ->join('item_categories', 'item_categories.id', '=', 'item_brands.item_category_id')
            ->join('users', 'users.id', '=', 'item_brands.user_id')
            ->where('item_brands.account_id', '=', $accountId)
//            ->where('item_brands.deleted_at', '=', null)
            ->select(
                'item_brands.id',
                'item_brands.public_id',
                'item_brands.name as item_brand_name',
                'item_brands.is_deleted',
                'item_brands.notes',
                'item_brands.created_at',
                'item_brands.updated_at',
                'item_brands.deleted_at',
                'item_brands.created_by',
                'item_brands.updated_by',
                'item_brands.deleted_by',
                'item_categories.name as item_category_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('item_brands.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_brands.notes', 'like', '%' . $filter . '%')
                    ->orWhere('item_categories.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_BRAND);

        return $query;
    }

    public function findItemCategory($itemCategoryPublicId)
    {
        $itemCategoryId = ItemCategory::getPrivateId($itemCategoryPublicId);

        $query = $this->find()->where('item_brands.item_category_id', '=', $itemCategoryId);

        return $query;
    }

    public function save($data, $itemBrand = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemBrand) {
            $itemBrand->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $itemBrand = ItemBrand::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $itemBrand = ItemBrand::createNew();
            $itemBrand->created_by = Auth::user()->username;
        }
        $itemBrand->fill($data);
        $itemBrand->name = isset($data['name']) ? trim($data['name']) : '';
        $itemBrand->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $itemBrand->save();

        return $itemBrand;
    }

    public function findPhonetically($itemBrandName)
    {
        $itemBrandNameMeta = metaphone($itemBrandName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemBrandId = 0;
        $itemBrands = ItemBrand::scope()->get();
        if (!empty($itemBrands)) {
            foreach ($itemBrands as $itemBrand) {
                if (!$itemBrand->name) {
                    continue;
                }
                $map[$itemBrand->id] = $itemBrand;
                $similar = similar_text($itemBrandNameMeta, metaphone($itemBrand->name), $percent);
                if ($percent > $max) {
                    $itemBrandId = $itemBrand->id;
                    $max = $percent;
                }
            }
        }

        return ($itemBrandId && isset($map[$itemBrandId])) ? $map[$itemBrandId] : null;
    }
}