<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class BillExpenseDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BILL_EXPENSE;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'vendor_name',
                function ($model) {
                    if ($model->vendor_public_id) {
                        if (Auth::user()->can('edit', [ENTITY_VENDOR, $model]))
                            return link_to("vendors/{$model->vendor_public_id}", $model->vendor_name)->toHtml();
                        else
                            return $model->vendor_name;

                    } else {
                        return $model->vendor_name;
                    }
                },
            ],
            [
                'vendor_name',
                function ($model) {
                    if ($model->vendor_public_id) {
                        if (Auth::user()->can('edit', [ENTITY_VENDOR, $model]))
                            return link_to("vendors/{$model->vendor_public_id}", Utils::getVendorDisplayName($model))->toHtml();
                        else
                            return Utils::getVendorDisplayName($model);

                    } else {
                        return Utils::getVendorDisplayName($model);
                    }
                },
            ],
            [
                'expense_date',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_BILL_EXPENSE, $model]))
                        return $this->addNote(link_to("purchase_BILL_EXPENSEs/{$model->public_id}/edit", Utils::fromSqlDate($model->expense_date_sql))->toHtml(), $model->private_notes);
                    else
                        return Utils::fromSqlDate($model->expense_date_sql);

                },
            ],
            [
                'invoice_number',
                function ($model) {
                    return $this->showWithTooltip($model->invoice_number);
                },
            ],
            [
                'amount',
                function ($model) {
                    $amount = $model->amount + Utils::calculateTaxes($model->amount, $model->tax_rate1, $model->tax_rate2);
                    $str = Utils::formatMoney($amount, $model->expense_currency_id);

                    // show both the amount and the converted amount
                    if ($model->exchange_rate != 1) {
                        $converted = round($amount * $model->exchange_rate, 2);
                        $str .= ' | ' . Utils::formatMoney($converted, $model->invoice_currency_id);
                    }

                    return $str;
                },
            ],
            [
                'category',
                function ($model) {
                    $category = $model->category != null ? substr($model->category, 0, 100) : '';
                    if (Auth::user()->can('edit', [ENTITY_BILL_EXPENSE, $model]))
                        return $model->category_public_id ? link_to("expense_categories/{$model->category_public_id}/edit", $category)->toHtml() : '';
                    else
                        return $category;

                },
            ],
            [
                'public_notes',
                function ($model) {
                    return $this->showWithTooltip($model->public_notes);
                },
            ],
            [
                'status',
                function ($model) {
                    return self::getStatusLabel($model->invoice_id, $model->should_be_invoiced, $model->balance, $model->payment_date);
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
                trans('texts.edit_BILL_EXPENSE'),
                function ($model) {
                    return URL::to("purchase_BILL_EXPENSEs/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BILL_EXPENSE, $model]);
                },
            ],
            [
                trans("texts.clone_BILL_EXPENSE"),
                function ($model) {
                    return URL::to("purchase_BILL_EXPENSEs/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_BILL_EXPENSE);
                },
            ],
            [
                trans('texts.edit_invoice'),
                function ($model) {
                    return URL::to("/bills/{$model->invoice_public_id}/edit");
                },
                function ($model) {
                    return $model->invoice_public_id && Auth::user()->can('edit', [ENTITY_BILL, $model]);
                },
            ],
            [
                trans('texts.invoice_BILL_EXPENSE'),
                function ($model) {
                    return "javascript:submitForm_BILL_EXPENSE('invoice', {$model->public_id})";
                },
                function ($model) {
                    return !$model->invoice_id && (!$model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('create', ENTITY_BILL);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BILL]);
                },
            ],
        ];
    }

    private function getStatusLabel($invoiceId, $shouldBeInvoiced, $balance, $paymentDate)
    {
        $label = Expense::calcStatusLabel($shouldBeInvoiced, $invoiceId, $balance, $paymentDate);
        $class = Expense::calcStatusClass($shouldBeInvoiced, $invoiceId, $balance);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }
}
