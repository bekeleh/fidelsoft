<?php

namespace App\Ninja\Repositories;


use App\Models\ItemCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ItemCategoryRepository extends BaseRepository
{
    private $model;

    public function __construct(ItemCategory $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ItemCategory';
    }

    public function all()
    {
        return ItemCategory::scope()->withTrashed()->where('is_deleted', '=', false)->get();
    }


    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('item_categories')
            ->LeftJoin('accounts', 'accounts.id', '=', 'item_categories.account_id')
            ->LeftJoin('users', 'users.id', '=', 'item_categories.user_id')
            ->where('item_categories.account_id', '=', $accountId)
//            ->where('item_categories.deleted_at', '=', null)
            ->select(
                'item_categories.id',
                'item_categories.public_id',
                'item_categories.name as item_category_name',
                'item_categories.is_deleted',
                'item_categories.notes',
                'item_categories.created_at',
                'item_categories.updated_at',
                'item_categories.deleted_at',
                'item_categories.created_by',
                'item_categories.updated_by',
                'item_categories.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('item_categories.name', 'like', '%' . $filter . '%')
                    ->orWhere('item_categories.notes', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_ITEM_CATEGORY);

        return $query;
    }

    public function save($data, $itemCategory = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($itemCategory) {
            $itemCategory->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $itemCategory = ItemCategory::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $itemCategory = ItemCategory::createNew();
            $itemCategory->created_by = Auth::user()->username;
        }
        $itemCategory->fill($data);
        $itemCategory->name = isset($data['name']) ? trim($data['name']) : '';
        $itemCategory->notes = isset($data['notes']) ? trim($data['notes']) : '';
        $itemCategory->save();
        return $itemCategory;
    }

    public function findPhonetically($itemCategoryName)
    {
        $itemCategoryNameMeta = metaphone($itemCategoryName);
        $map = [];
        $max = SIMILAR_MIN_THRESHOLD;
        $itemCategoryId = 0;
        $itemCategories = ItemCategory::scope()->get();
        if (!empty($itemCategories)) {
            foreach ($itemCategories as $itemCategory) {
                if (!$itemCategory->name) {
                    continue;
                }
                $map[$itemCategory->id] = $itemCategory;
                $similar = similar_text($itemCategoryNameMeta, metaphone($itemCategory->name), $percent);
                if ($percent > $max) {
                    $itemCategoryId = $itemCategory->id;
                    $max = $percent;
                }
            }
        }

        return ($itemCategoryId && isset($map[$itemCategoryId])) ? $map[$itemCategoryId] : null;
    }
}