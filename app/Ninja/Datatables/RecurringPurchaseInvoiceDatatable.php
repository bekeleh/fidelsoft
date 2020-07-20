<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;
use App\Models\PurchaseInvoice;

class RecurringPurchaseInvoiceDatatable extends EntityDatatable
{
    public $entityType = ENTITY_RECURRING_PURCHASE_INVOICE;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'frequency',
                function ($model) {
                    if ($model->frequency) {
                        $frequency = strtolower($model->frequency);
                        $frequency = preg_replace('/\s/', '_', $frequency);
                        $label = trans('texts.freq_' . $frequency);
                    } else {
                        $label = trans('texts.freq_inactive');
                    }

                    return link_to("recurring_purchase_purchase_purchase_invoices/{$model->public_id}/edit", $label)->toHtml();
                },
            ],
            [
                'vendor_name',
                function ($model) {
                    if (auth::user()->can('edit', [ENTITY_CLIENT])) {
                        return link_to("vendors/{$model->client_public_id}", Utils::getClientDisplayName($model))->toHtml();
                    } else {
                        return Utils::getClientDisplayName($model);
                    }
                },
            ],
            [
                'start_date',
                function ($model) {
                    return Utils::fromSqlDate($model->start_date_sql);
                },
            ],
            [
                'last_sent',
                function ($model) {
                    return Utils::fromSqlDate($model->last_sent_date_sql);
                },
            ],
            /*
            [
                'end_date',
                function ($model) {
                    return Utils::fromSqlDate($model->end_date_sql);
                },
            ],
            */
            [
                'amount',
                function ($model) {
                    return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
                },
            ],
            [
                'public_notes',
                function ($model) {
                    return $this->showWithTooltip($model->public_notes);
                },
            ],
            [
                'private_notes',
                function ($model) {
                    return $this->showWithTooltip($model->private_notes);
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

    private function getStatusLabel($model)
    {
        $class = PurchaseInvoice::calcStatusClass($model->invoice_status_id, $model->balance, $model->due_date_sql, $model->is_recurring);
        $label = PurchaseInvoice::calcStatusLabel($model->invoice_status_name, $class, $this->entityType, $model->quote_purchase_invoice_id);

        if ($model->invoice_status_id == INVOICE_STATUS_SENT) {
            if (!$model->last_sent_date_sql || $model->last_sent_date_sql == '0000-00-00') {
                $label = trans('texts.pending');
            } else {
                $label = trans('texts.active');
            }
        }

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }

    public function actions()
    {
        return [
            [
                trans('texts.edit_purchase_invoice'),
                function ($model) {
                    return URL::to("purchase_invoices/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_PURCHASE_INVOICE, $model]);
                },
            ],
            [
                trans("texts.clone_purchase_invoice"),
                function ($model) {
                    return URL::to("purchase_invoices/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PURCHASE_INVOICE);
                },
            ],
            [
                trans("texts.clone_purchase_quote"),
                function ($model) {
                    return URL::to("purchase_quotes/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_PURCHASE_QUOTE);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_PURCHASE_INVOICE]);
                },
            ],

        ];
    }
}
