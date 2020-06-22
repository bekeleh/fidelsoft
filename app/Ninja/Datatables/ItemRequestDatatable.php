<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemRequestDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ITEM_REQUEST;
    public $sortCol = 1;

    public function columns()
    {
        $account = Auth::user()->account;

        return [
            [
                'product_key',
                function ($model) {
                    if ($model->product_public_id) {
                        return link_to("products/{$model->product_public_id}", $model->product_key)->toHtml();
                    } else {
                        return $model->product_key;
                    }
                },
            ],
            [
                'department_name',
                function ($model) {
                    if ($model->department_public_id) {
                        return link_to("departments/{$model->department_public_id}", $model->department_name)->toHtml();
                    } else {
                        return $model->department_name;
                    }
                },
            ],
            [
                'store_name',
                function ($model) {
                    if ($model->store_public_id) {
                        return link_to("stores/{$model->store_public_id}", $model->store_name)->toHtml();
                    } else {
                        return $model->store_name;
                    }
                },
            ],
            [
                'status_name',
                function ($model) {
                    return Self::getStatusLabel($model);
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
                    return Auth::user()->can('edit', ENTITY_ITEM_REQUEST) && $model->status_id != Utils::getStatusId('approved');
                },
            ],
            [
                trans('texts.clone_item_request'),
                function ($model) {
                    return URL::to("item_requests/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_ITEM_REQUEST);
                },
            ],
            [
                trans('texts.approve'),
                function ($model) {
                    return URL::to("item_requests/{$model->public_id}");
                },
                function ($model) {
                    return Utils::isAdmin() && $model->status_id != Utils::getStatusId('approved');
                }
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_ITEM_REQUEST, $model]);
                },

            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = ItemRequest::getStdStatus(Utils::getStatusName($model->status_id));

        return "<h4><div class=\"label label-{$class}\">$model->status_name</div></h4>";
    }
}
