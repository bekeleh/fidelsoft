<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class ClientDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CLIENT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'id_number',
                function ($model) {
                    return $model->id_number;
                },
                Auth::user()->account->clientNumbersEnabled()
            ],
            [
                'client_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_CLIENT])) {
                        $str = link_to("clients/{$model->public_id}", $model->client_name ?: '')->toHtml();
                        return $this->addNote($str, $model->private_notes);
                    } else {
                        return $model->client_name;
                    }
                },
            ],
            [
                'balance',
                function ($model) {
                    return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },
            ],
            [
                'contact',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_CLIENT_CONTACT])) {
                        return link_to("contacts/{$model->contact_public_id}",
                            $model->contact ?: '')->toHtml();
                    } else {
                        return $model->contact;
                    }
                },
            ],
            [
                'work_phone',
                function ($model) {
                    return $model->work_phone;
                },
            ],
            [
                'email',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_CLIENT])) {
                        return link_to("clients/{$model->public_id}", $model->email ?: '')->toHtml();
                    } else {
                        return $model->contact;
                    }
                },
            ],
            [
                'client_type_name',
                function ($model) {
                    return $model->client_type_name;
                },
            ],
            [
                'sale_type_name',
                function ($model) {
                    return $model->sale_type_name;
                },
            ],
            [
                'hold_reason_name',
                function ($model) {
                    return $model->hold_reason_name;
                },
            ],
            [
                'public_notes',
                function ($model) {
                    return $model->public_notes;
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
                    return Auth::user()->can('edit', ENTITY_CLIENT) && $model->user_id === Auth::user()->id;
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
                trans('texts.new_task'),
                function ($model) {
                    return URL::to("tasks/create/{$model->public_id}");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_TASK);
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
