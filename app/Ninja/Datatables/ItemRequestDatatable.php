<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use phpDocumentor\Reflection\Types\Self_;

class ItemRequestDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_REQUEST;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'product_name',
                function ($model) {
                    if ($model->product_public_id) {
                        if (Auth::user()->can('view', [ENTITY_PRODUCT, $model]))
                            return link_to("products/{$model->product_public_id}", $model->product_name)->toHtml();
                        else
                            return $model->product_name;
                    }
                    return null;
                },
            ],
            [
                'department_name',
                function ($model) {
                    if ($model->department_public_id) {
                        if (Auth::user()->can('view', [ENTITY_DEPARTMENT, $model]))
                            return link_to("departments/{$model->department_public_id}", $model->department_name)->toHtml();
                        else
                            return $model->department_name;
                    }
                    return null;
                },
            ],
            [
                'store_name',
                function ($model) {
                    if ($model->store_public_id) {
                        if (Auth::user()->can('view', [ENTITY_STORE, $model]))
                            return link_to("stores/{$model->store_public_id}", $model->store_name)->toHtml();
                        else
                            return $model->store_name;
                    }
                    return null;
                },
            ],
            [
                'status_name',
                function ($model) {
                    if ($model->status_public_id) {
                        if (Auth::user()->can('view', [ENTITY_STATUS, $model]))
                            //                            return link_to("statuses/{$model->status_public_id}", $model->status_name)->toHtml();
                            return Self::getStatusLabel($model);

                        else
                            return Self::getStatusLabel($model);
                    }
                    return null;
                },
            ],
            [
                'qty',
                function ($model) {
                    return $model->qty;
                },
            ],
            [
                'delivered_qty',
                function ($model) {
                    return $model->delivered_qty;
                },
            ],
            [
                'notes',
                function ($model) {
                    return $model->notes;
                },
            ],
            [
                'required_date',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->required_date));
                },
            ],
            [
                'dispatch_date',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->dispatch_date));
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
                uctrans('texts.edit_item_request'),
                function ($model) {
                    return URL::to("item_requests/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_ITEM_REQUEST);
                },
            ],
//            [
//                trans('texts.clone_item_request'),
//                function ($model) {
//                    return URL::to("item_requests/{$model->public_id}/clone");
//                },
//                function ($model) {
//                    return Auth::user()->can('create', ENTITY_ITEM_REQUEST);
//                },
//            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = ItemRequest::getStdStatus(Utils::getStatusName($model->status_id));

        return "<h4><div class=\"label label-{$class}\">$model->status_name</div></h4>";
    }
}
