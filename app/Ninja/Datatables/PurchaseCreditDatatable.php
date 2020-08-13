<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class PurchaseCreditDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BILL_CREDIT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'vendor_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR, $model])) {

                        $str = $model->vendor_public_id ? link_to("vendors/{$model->vendor_public_id}", Utils::getClientDisplayName($model))->toHtml() : '';
                        return $this->addNote($str, $model->private_notes);
                    } else {
                        return Utils::getClientDisplayName($model);
                    }

                },
            ],
            [
                'amount',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR, $model]))
                        return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id) . '<span ' . Utils::getEntityRowClass($model) . '/>';
                },
            ],
            [
                'balance',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_VENDOR, $model]))
                        return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },
            ],
            [
                'credit_date',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_BILL_CREDIT, $model]))
                        return link_to("BILL_CREDITs/{$model->public_id}/edit", Utils::fromSqlDate($model->credit_date_sql))->toHtml();
                    else
                        return Utils::fromSqlDate($model->credit_date_sql);

                },
            ],
            [
                'public_notes',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_BILL_CREDIT, $model]))
                        return e($model->public_notes);
                },
            ],
            // [
            //     'private_notes',
            //     function ($model) {
            //         if (Auth::user()->can('view', [ENTITY_BILL_CREDIT, $model]))
            //             return e($model->private_notes);
            //     },
            // ],
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
                trans('texts.edit_BILL_CREDIT'),
                function ($model) {
                    return URL::to("BILL_CREDITs/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BILL_CREDIT, $model]);
                },
            ],
            [
                trans('texts.clone_BILL_CREDIT'),
                function ($model) {
                    return URL::to("BILL_CREDITs/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_BILL_CREDIT, $model]);
                },
            ],
            [
                trans('texts.apply_BILL_CREDIT'),
                function ($model) {
                    return URL::to("BILL_PAYMENTs/create/{$model->vendor_public_id}") . '?paymentTypeId=1';
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
