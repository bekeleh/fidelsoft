<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class VendorCreditDatatable extends EntityDatatable
{
    public $entityType = ENTITY_VENDOR_CREDIT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'vendor_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR, $model])) {
                        $str = $model->vendor_public_id ? link_to("vendors/{$model->vendor_public_id}", Utils::getVendorDisplayName($model))->toHtml() : '';
                        return $this->addNote($str, $model->private_notes);
                    } else {
                        return Utils::getVendorDisplayName($model);
                    }
                },
            ],
            [
                'amount',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR, $model])) {
                        return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id) . '<span ' . Utils::getEntityRowClass($model) . '/>';
                    } else {
                        return '';
                    }
                },
            ],
            [
                'balance',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR, $model])) {
                        return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                    } else {
                        return '';
                    }
                },
            ],
            [
                'credit_date',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR_CREDIT, $model])) {
                        return link_to("vendor_credits/{$model->public_id}/edit", Utils::fromSqlDate($model->credit_date_sql))->toHtml();
                    } else {
                        return Utils::fromSqlDate($model->credit_date_sql);
                    }
                },
            ],
            [
                'public_notes',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR_CREDIT, $model])) {
                        return e($model->public_notes);
                    } else {
                        return '';
                    }
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
                trans('texts.edit_credit'),
                function ($model) {
                    return URL::to("vendor_credits/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_VENDOR_CREDIT, $model]);
                },
            ],
            [
                trans('texts.clone_credit'),
                function ($model) {
                    return URL::to("vendor_credits/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_VENDOR_CREDIT, $model]);
                },
            ],
            [
                trans('texts.apply_credit'),
                function ($model) {
                    return URL::to("bill_payments/create/{$model->vendor_public_id}") . '?paymentTypeId=1';
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_BILL_PAYMENT);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BILL_PAYMENT]);
                },
            ],
        ];
    }
}
