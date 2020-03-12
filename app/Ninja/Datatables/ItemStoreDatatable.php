<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemStoreDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_STORE;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'product_name',
                function ($model) {
                    if ($model->product_id) {
                        if (Auth::user()->can('view', [ENTITY_PRODUCT, $model]))
                            return link_to("products/{$model->product_id}", $model->product_name)->toHtml();
                        else
                            return $model->product_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'store_name',
                function ($model) {
                    if ($model->store_id) {
                        if (Auth::user()->can('view', [ENTITY_ITEM_STORE, $model]))
                            return link_to("stores/{$model->store_id}", $model->store_name)->toHtml();
                        else
                            return $model->store_name;
                    } else {
                        return '';
                    }
                }
            ],
            [
                'bin',
                function ($model) {
                    return link_to('item_stores/' . $model->public_id . '/edit', $model->bin)->toHtml();
                },
            ],

            [
                'qty',
                function ($model) {
                    return $model->qty;
                },
            ], [
                'notes',
                function ($model) {
                    return $this->showWithTooltip($model->notes);
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
                uctrans('texts.edit_item_store'),
                function ($model) {
                    return URL::to("item_stores/{$model->public_id}/edit");
                },
            ],
            [
                trans('texts.clone_item_store'),
                function ($model) {
                    return URL::to("item_stores/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_STORE);
                },
            ],
        ];
    }
}
