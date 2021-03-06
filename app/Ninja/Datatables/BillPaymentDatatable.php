<?php

namespace App\Ninja\Datatables;

use App\Models\BillPayment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class BillPaymentDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BILL_PAYMENT;
    public $sortCol = 1;

    protected static $refundableGateways = [
        GATEWAY_STRIPE,
        GATEWAY_BRAINTREE,
        GATEWAY_WEPAY,
    ];

    public function columns()
    {
        return [
            [
                'invoice_number',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_BILL])) {
                        return link_to("bills/{$model->bill_public_id}/edit", $model->invoice_number, ['class' => Utils::getEntityRowClass($model)])->toHtml();
                    } else {
                        return $model->invoice_number;
                    }
                },
            ],
            [
                'vendor_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_VENDOR])) {
                        return $model->vendor_public_id ? link_to("vendors/{$model->vendor_public_id}", Utils::getVendorDisplayName($model))->toHtml() : '';
                    } else {
                        return Utils::getVendorDisplayName($model);
                    }
                },
            ],
            [
                'amount',
                function ($model) {
                    $amount = Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);

                    if ($model->exchange_currency_id && $model->exchange_rate != 1) {
                        $amount .= ' | ' . Utils::formatMoney($model->amount * $model->exchange_rate, $model->exchange_currency_id, $model->country_id);
                    }

                    return $amount;
                },
            ],
            [
                'transaction_reference',
                function ($model) {
                    $str = $model->transaction_reference ? e($model->transaction_reference) : '<i>' . trans('texts.manual_entry') . '</i>';
                    return $this->addNote($str, $model->private_notes);
                    // return $str;
                },
            ],
            [
                'public_notes',
                function ($model) {
                    return $model->public_notes;
                },
            ],
            [
                'method',
                function ($model) {
                    return $model->account_gateway_id ? $model->gateway_name : ($model->payment_type ? trans('texts.payment_type_' . $model->payment_type) : '');
                },
            ],
            [
                'source',
                function ($model) {
                    $code = str_replace(' ', '', strtolower($model->payment_type));
                    $card_type = trans('texts.card_' . $code);
                    if ($model->payment_type_id != PAYMENT_TYPE_ACH) {
                        if ($model->last4) {
                            $expiration = Utils::fromSqlDate($model->expiration, false)->format('m/y');

                            return '<img height="22" src="' . URL::to('/images/credit_cards/' . $code . '.png') . '" alt="' . htmlentities($card_type) . '">&nbsp; &bull;&bull;&bull;' . $model->last4 . ' ' . $expiration;
                        } elseif ($model->email) {
                            return $model->email;
                        } elseif ($model->payment_type) {
                            return trans('texts.payment_type_' . $model->payment_type);
                        }
                    } elseif ($model->last4) {
                        if ($model->bank_name) {
                            $bankName = $model->bank_name;
                        } else {
                            $bankData = PaymentMethod::lookupBankData($model->routing_number);
                            if ($bankData) {
                                $bankName = $bankData->name;
                            }
                        }
                        if (!empty($bankName)) {
                            return $bankName . '&nbsp; &bull;&bull;&bull;' . $model->last4;
                        } elseif ($model->last4) {
                            return '<img height="22" src="' . URL::to('/images/credit_cards/ach.png') . '" alt="' . htmlentities($card_type) . '">&nbsp; &bull;&bull;&bull;' . $model->last4;
                        }
                    }
                },
            ],
            [
                'date',
                function ($model) {
                    if ($model->is_deleted) {
                        return Utils::dateToString($model->payment_date);
                    } else {
                        return link_to("bill_payments/{$model->public_id}/edit", Utils::dateToString($model->payment_date))->toHtml();
                    }
                },
            ],
            [
                'status',
                function ($model) {
                    return self::getStatusLabel($model);
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
                trans('texts.edit_payment'),
                function ($model) {
                    return URL::to("bill_payments/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BILL_PAYMENT, $model]);
                },
            ],
            [
                trans('texts.clone_payment'),
                function ($model) {
                    return URL::to("bill_payments/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_BILL_PAYMENT, $model]);
                },
            ],
            [
                trans('texts.email_payment'),
                function ($model) {
                    return "javascript:submitForm_payment('email', {$model->public_id})";
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BILL_PAYMENT, $model]);
                },
            ],
            [
                trans('texts.refund_payment'),
                function ($model) {
                    $max_refund = $model->amount - $model->refunded;
                    $formatted = Utils::formatMoney($max_refund, $model->currency_id, $model->country_id);
                    $symbol = Utils::getFromCache($model->currency_id ? $model->currency_id : 1, 'currencies')->symbol;
                    $local = in_array($model->gateway_id, [GATEWAY_BRAINTREE, GATEWAY_STRIPE, GATEWAY_WEPAY]) || !$model->gateway_id ? 0 : 1;

                    return "javascript:showRefundModal({$model->public_id}, '{$max_refund}', '{$formatted}', '{$symbol}', {$local})";
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BILL_PAYMENT, $model])
                        && $model->payment_status_id >= PAYMENT_STATUS_COMPLETED
                        && $model->refunded < $model->amount;
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

    private function getStatusLabel($model)
    {
        $amount = Utils::formatMoney($model->refunded, $model->currency_id, $model->country_id);
        $label = BillPayment::calcStatusLabel($model->payment_status_id, $model->status, $amount);
        $class = BillPayment::calcStatusClass($model->payment_status_id);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }
}
