<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;

class ActivityVendorDatatable extends EntityDatatable
{
    public $entityType = ENTITY_ACTIVITY;

    public function columns()
    {
        return [
            [
                'activities.id',
                function ($model) {
                    $str = Utils::timestampToDateTimeString(strtotime($model->created_at));
                    $activityTypes = [
                        ACTIVITY_TYPE_VIEW_BILL_QUOTE,
                        ACTIVITY_TYPE_VIEW_BILL_QUOTE,
                        ACTIVITY_TYPE_CREATE_BILL_PAYMENT,
                        ACTIVITY_TYPE_APPROVE_BILL_QUOTE,
                    ];

                    if ($model->contact_id
                        && !$model->is_system
                        && in_array($model->activity_type_id, $activityTypes)
                        && !in_array($model->ip, ['127.0.0.1', '192.168.255.33'])
                    ) {
                        $ipLookUpLink = IP_LOOKUP_URL . $model->ip;
                        $str .= sprintf(' &nbsp; <i class="fa fa-globe" style="cursor:pointer" title="%s" onclick="openUrl(\'%s\', \'IP Lookup\')"></i>', $model->ip, $ipLookUpLink);
                    } elseif ($model->token_id) {
                        $str .= ' &nbsp; <i class="fa fa-server" title="API"><i>';
                    }

                    return $str;
                },
            ],
            [
                'activity_type_id',
                function ($model) {
                    $data = [
                        'vendor' => link_to('/vendors/' . $model->vendor_public_id, Utils::getVendorDisplayName($model))->toHtml(),
                        'user' => $model->is_system ? '<i>' . trans('texts.system') . '</i>' : Utils::getPersonDisplayName($model->user_first_name, $model->user_last_name, $model->user_email),
                        'BILL' => $model->invoice_number ? link_to('/BILLs/' . $model->invoice_public_id, $model->is_recurring ? trans('texts.recurring_invoice') : $model->invoice_number)->toHtml() : null,
                        'BILL_QUOTE' => $model->invoice ? link_to('/BILL_QUOTEs/' . $model->invoice_public_id, $model->invoice)->toHtml() : null,
                        'vendor_contact' => $model->contact_id ? link_to('/vendors/' . $model->vendor_public_id, Utils::getPersonDisplayName($model->first_name, $model->last_name, $model->email))->toHtml() : Utils::getPersonDisplayName($model->user_first_name, $model->user_last_name, $model->user_email),
                        'BILL_PAYMENT' => $model->payment ? e($model->payment) : '',
                        'BILL_CREDIT' => $model->payment_amount ? Utils::formatMoney($model->credit, $model->currency_id, $model->country_id) : '',
                        'payment_amount' => $model->payment_amount ? Utils::formatMoney($model->payment_amount, $model->currency_id, $model->country_id) : null,
                        'adjustment' => $model->adjustment ? Utils::formatMoney($model->adjustment, $model->currency_id, $model->country_id) : null,
                        'BILL_EXPENSE' => $model->expense_public_id ? link_to('/BILL_EXPENSEs/' . $model->expense_public_id, substr($model->expense_public_notes, 0, 30) . '...') : null,
                    ];

                    $str = trans("texts.activity_{$model->activity_type_id}", $data);

                    if ($model->notes) {
                        $str .= ' - ' . trans("texts.notes_{$model->notes}");
                    }

                    return $str;
                },
            ],
            [
                'balance',
                function ($model) {
                    return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },
            ],
            [
                'adjustment',
                function ($model) {
                    return $model->adjustment != 0 ? Utils::wrapAdjustment($model->adjustment, $model->currency_id, $model->country_id) : '';
                },
            ],
        ];
    }
}
