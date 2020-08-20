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
                'tax_rate_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_TAX_RATE]))
                        return link_to("tax_rates/{$model->public_id}", $model->tax_rate_name ?: '')->toHtml();
                    else
                        return $model->tax_rate_name;
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
//                    if (auth()->user()->account->inclusive_taxes) {
                    if ($model->is_inclusive) {
                        return trans('texts.inclusive');
                    } else {
                        return $model->is_inclusive ? trans('texts.inclusive') : trans('texts.exclusive');
                    }
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
                    return Auth::user()->can('edit', [ENTITY_TAX_RATE]);
                },
            ],
        ];
    }
}
