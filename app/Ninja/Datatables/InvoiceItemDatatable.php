<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class InvoiceItemDatatable extends EntityDatatable
{
    public $entityType = ENTITY_INVOICE_ITEM;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'invoice_number',
                function ($model) {
                    if ($model->public_id) {
                        return link_to("invoices/{$model->public_id}", $model->invoice_number ?: '')->toHtml();
                    } else {
                        return $model->invoice_number;
                    }
                },
            ],
            [
                'invoice_item_name',
                function ($model) {
                    if ($model->public_id) {
                        return link_to("invoice_items/{$model->public_id}", $model->invoice_item_name ?: '')->toHtml();
                    } else {
                        return $model->invoice_item_name;
                    }
                },
            ],
            [
                'product_key',
                function ($model) {
                    if ($model->product_public_id) {
                        return link_to("products/{$model->product_public_id}", $model->product_key ?: '')->toHtml();
                    } else {
                        return $model->product_key;
                    }
                },
            ],
            [
                trans('invoiced_qty'),
                function ($model) {
                    return $this->showWithTooltip($model->qty);
                },
            ],
            [
                trans('demand_qty'),
                function ($model) {
                    return $this->showWithTooltip($model->demand_qty);
                },
            ],
            [
                trans('cost'),
                function ($model) {
                    return $this->showWithTooltip($model->cost);
                },
            ],
            [
                'discount',
                function ($model) {
                    return $this->showWithTooltip($model->discount);
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
                uctrans('texts.edit_invoice_item'),
                function ($model) {
                    return URL::to("invoice_items/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_INVOICE_ITEM);
                },
            ],
            [
                uctrans('texts.clone_invoice_item'),
                function ($model) {
                    return URL::to("invoice_items/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_INVOICE_ITEM);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_INVOICE_ITEM]);
                },
            ],
        ];
    }
}
