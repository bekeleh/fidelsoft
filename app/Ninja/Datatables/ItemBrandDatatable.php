<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class ItemBrandDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_BRAND;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'item_brand_name',
                function ($model) {
                    $str = link_to("item_brands/{$model->public_id}", $model->item_brand_name ?: '')->toHtml();
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
                trans('texts.edit_item_brand'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_ITEM_BRAND, $model]))
                        return URL::to("item_brands/{$model->public_id}/edit");
                    elseif (Auth::user()->can('view', [ENTITY_ITEM_BRAND, $model]))
                        return URL::to("item_brands/{$model->public_id}");
                },
            ],
            [
                trans('texts.clone_item_brand'),
                function ($model) {
                    return URL::to("item_brands/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_BRAND);
                },
            ],
        ];
    }
}
