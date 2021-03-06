<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class ClientContactDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CLIENT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'client_name',
                function ($model) {
                    $str = link_to("clients/{$model->public_id}", $model->client_name ?: '')->toHtml();
                    return $this->addNote($str, $model->private_notes);
                },
            ],
            [
                'contact',
                function ($model) {
                    return link_to("clients/{$model->public_id}", $model->contact ?: '')->toHtml();
                },
            ],
            [
                'email',
                function ($model) {
                    return link_to("clients/{$model->public_id}", $model->email ?: '')->toHtml();
                },
            ],
            [
                'id_number',
                function ($model) {
                    return $model->id_number;
                },
                Auth::user()->account->clientNumbersEnabled()
            ],
            [
                'client_type_name',
                function ($model) {
                    if ($model->client_type_public_id)
                        return link_to("client_types/{$model->client_type_public_id}", $model->client_type_name ?: '')->toHtml();
                    else
                        return $model->client_type_name;
                },
            ],
            [
                'sale_type_name',
                function ($model) {
                    if ($model->sale_type_public_id)
                        return link_to("sale_types/{$model->sale_type_public_id}", $model->sale_type_name ?: '')->toHtml();
                    else
                        $model->sale_type_name;
                },
            ],
            [
                'hold_reason_name',
                function ($model) {
                    return link_to("hold_reasons/{$model->hold_reason_public_id}", $model->hold_reason_name ?: '')->toHtml();
                },
            ],
            [
                'client_created_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
            [
                'last_login',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->last_login));
                },
            ],
            [
                'balance',
                function ($model) {
                    return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
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
                trans('texts.edit_client'),
                function ($model) {
                    return URL::to("clients/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', ENTITY_CLIENT);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_CLIENT, $model]) && (Auth::user()->can('create', ENTITY_TASK) || Auth::user()->can('create', ENTITY_INVOICE));
                },
            ],
            [
                trans('texts.new_task'),
                function ($model) {
                    return URL::to("tasks/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_TASK);
                },
            ],
            [
                trans('texts.new_invoice'),
                function ($model) {
                    return URL::to("invoices/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
            [
                trans('texts.new_quote'),
                function ($model) {
                    return URL::to("quotes/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->hasFeature(FEATURE_QUOTES) && Auth::user()->can('create', ENTITY_QUOTE);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return (Auth::user()->can('create', ENTITY_TASK) || Auth::user()->can('create', ENTITY_INVOICE)) && (Auth::user()->can('create', ENTITY_PAYMENT) || Auth::user()->can('create', ENTITY_CREDIT) || Auth::user()->can('create', ENTITY_EXPENSE));
                },
            ],
            [
                trans('texts.enter_payment'),
                function ($model) {
                    return URL::to("payments/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.enter_credit'),
                function ($model) {
                    return URL::to("credits/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_CREDIT);
                },
            ],
            [
                trans('texts.enter_expense'),
                function ($model) {
                    return URL::to("expenses/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_EXPENSE);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_EXPENSE]);
                },
            ],
        ];
    }
}
