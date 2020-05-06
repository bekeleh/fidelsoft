<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class PaymentTermDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PAYMENT_TERM;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'num_days',
                function ($model) {
                    return link_to("payment_terms/{$model->public_id}/edit", trans('texts.payment_terms_net') . ' ' . ($model->num_days == -1 ? 0 : $model->num_days))->toHtml();
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_payment_term'),
                function ($model) {
                    return URL::to("payment_terms/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_PAYMENT_TERM);
                },
            ],
            [
                trans('texts.clone_payment_term'),
                function ($model) {
                    return URL::to("payment_terms/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PAYMENT_TERM);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_PAYMENT_TERM]);
                },
            ],
        ];
    }
}
