<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class TaxRateDatatable extends EntityDatatable
{
    public $entityType = ENTITY_TAX_RATE;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'name',
                function ($model) {
                    return link_to("tax_rates/{$model->public_id}/edit", $model->name)->toHtml();
                },
            ],
            [
                'rate',
                function ($model) {
                    return ($model->rate + 0) . '%';
                },
            ],
            [
                'type',
                function ($model) {
                    if (auth()->user()->account->inclusive_taxes) {
                        return trans('texts.inclusive');
                    } else {
                        return $model->is_inclusive ? trans('texts.inclusive') : trans('texts.exclusive');
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
                uctrans('texts.edit_tax_rate'),
                function ($model) {
                    return URL::to("tax_rates/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_TAX_RATE);
                },
            ],
            [
                uctrans('texts.clone_tax_rate'),
                function ($model) {
                    return URL::to("tax_rates/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_TAX_RATE);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_TAX_RATE, $model]);
                },

            ],
        ];
    }
}
