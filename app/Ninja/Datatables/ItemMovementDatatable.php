<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemMovementDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_MOVEMENT;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'item_name',
                function ($model) {
                    return link_to('item_movements/' . $model->public_id . '/edit', $model->item_name)->toHtml();
                },
            ],
            [
                'item_brand_name',
                function ($model) {
                    return link_to('item_brands/' . $model->public_id . '/edit', $model->item_brand_name)->toHtml();
                },
            ],
            [
                'item_category_name',
                function ($model) {
                    return link_to('item_categories/' . $model->public_id . '/edit', $model->item_category_name)->toHtml();
                },
            ],
            [
                'store_name',
                function ($model) {
                    return link_to('item_movements/' . $model->public_id . '/edit', $model->store_name)->toHtml();
                },
            ],
            [
                'qty',
                function ($model) {
                    return $model->qty;
                },
            ],
            [
                'qoh',
                function ($model) {
                    return $model->qoh;
                },
            ],
            [
                'notes',
                function ($model) {
                    return $this->showWithTooltip($model->notes);
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
        ];
    }

    public function actions()
    {
        return [
            [
                uctrans('texts.edit_item_movement'),
                function ($model) {
                    return URL::to("item_movements/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_ITEM_MOVEMENT);
                },
            ],
            [
                trans('texts.clone_item_movement'),
                function ($model) {
                    return URL::to("item_movements/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_MOVEMENT);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_ITEM_MOVEMENT]);
                },
            ],
        ];
    }
}
