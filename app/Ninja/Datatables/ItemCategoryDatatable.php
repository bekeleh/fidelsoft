<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class ItemCategoryDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_CATEGORY;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'item_category_name',
                function ($model) {
                    $str = link_to("item_categories/{$model->public_id}", $model->item_category_name ?: '')->toHtml();
                    return $str;
                },
            ],
            [
                'notes',
                function ($model) {
                    return $model->notes;
                },
            ],
            [
                'created_by',
                function ($model) {
                    return $model->created_by;
                },
            ],
            [
                'updated_by',
                function ($model) {
                    return $model->updated_by;
                },
            ],
            [
                'created_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
            [
                'updated_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->updated_at));
                },
            ],
//            [
//                'date_deleted',
//                function ($model) {
//                    return Utils::timestampToDateString(strtotime($model->deleted_at));
//                },
//            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_item_category'),
                function ($model) {
                    return URL::to("item_categories/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_ITEM_CATEGORY);
                },
            ],
            [
                trans('texts.clone_item_category'),
                function ($model) {
                    return URL::to("item_categories/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_CATEGORY);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_ITEM_CATEGORY]);
                },
            ],
        ];
    }
}
