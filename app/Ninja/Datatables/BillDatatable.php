<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use App\Models\Bill;
use DropdownButton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class BillDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BILL;
    public $sortCol = 1;

    public function columns()
    {
        $entityType = $this->entityType;

        return [
            [
                ENTITY_BILL ? 'invoice_number' : 'quote_number',
                function ($model) {
                    if (Auth::user()->can('edit', $this->entityType)) {
                        $str = link_to("{$this->entityType}s/{$model->public_id}/edit", $model->invoice_number, ['class' => Utils::getEntityRowClass($model)])->toHtml();
                        return $this->addNote($str, $model->private_notes);
                    } else
                        return $model->invoice_number;
                },
            ],
            [
                'vendor_name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_VENDOR]))
                        return link_to("vendors/{$model->vendor_public_id}", Utils::getVendorDisplayName($model))->toHtml();
                    else
                        return Utils::getVendorDisplayName($model);

                },
            ],
            [
                'date',
                function ($model) {
                    return Utils::fromSqlDate($model->bill_date);
                },
            ],
            [
                'amount',
                function ($model) {
                    return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
                },
            ],
            [
                'balance',
                function ($model) {
                    return $model->partial > 0 ?
                        trans('texts.partial_remaining', [
                                'partial' => Utils::formatMoney($model->partial, $model->currency_id, $model->country_id),
                                'balance' => Utils::formatMoney($model->balance, $model->currency_id, $model->country_id),]
                        ) :
                        Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },

                $entityType == ENTITY_BILL,
            ],
            [
                'discount',
                function ($model) {
                    return $model->discount;
                },
            ],
            [
                $entityType == ENTITY_BILL ? 'due_date' : 'valid_until',
                function ($model) {
                    $str = '';
                    if ($model->partial_due_date) {
                        $str = Utils::fromSqlDate($model->partial_due_date);
                        if ($model->due_date_sql && $model->due_date_sql != '0000-00-00') {
                            $str .= ', ';
                        }
                    }
                    return $str . Utils::fromSqlDate($model->due_date_sql);
                },
            ],
            [
                'status',
                function ($model) use ($entityType) {
                    return $model->quote_bill_id ? link_to("bills/{$model->quote_bill_id}/edit", trans('texts.converted'))->toHtml() : self::getStatusLabel($model);
                },
            ],
            [
                'public_notes',
                function ($model) {
                    return $model->public_notes;
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
//            [
//                'deleted_by',
//                function ($model) {
//                    return $model->updated_by;
//                },
//            ],
        ];
    }

    public function actions()
    {
        $entityType = $this->entityType;

        return [
            [
                trans('texts.edit_bill'),
                function ($model) {
                    return URL::to("bills/{$model->public_id}/edit");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_BILL && Auth::user()->can('edit', [ENTITY_BILL]) && $model->user_id == auth()->id();
                },
            ],
            [
                trans("texts.clone_bill"),
                function ($model) {
                    return URL::to("bills/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_BILL);
                },
            ],
            [
                trans("texts.clone_quote"),
                function ($model) {
                    return URL::to("bill_quotes/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_QUOTE);
                },
            ],
            [
                trans("texts.{$entityType}_history"),
                function ($model) use ($entityType) {
                    return URL::to("{$entityType}s/{$entityType}_history/{$model->public_id}");
                },
            ],
            [
                trans('texts.receive_note'),
                function ($model) use ($entityType) {
                    return url("bills/receive_note/{$model->public_id}");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_BILL;
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->canCreateOrEdit(ENTITY_BILL);
                },
            ],
            [
                trans('texts.convert_to_bill'),
                function ($model) {
                    return "javascript:submitForm_quote('convert', {$model->public_id})";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_QUOTE && !$model->quote_bill_id && Auth::user()->can('edit', [ENTITY_BILL, $model]);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->canCreateOrEdit(ENTITY_BILL);
                },
            ],
            [
                trans('texts.mark_sent'),
                function ($model) use ($entityType) {
                    return "javascript:submitForm_{$entityType}('markSent', {$model->public_id})";
                },
                function ($model) {
                    return !$model->is_public && Auth::user()->can('edit', [ENTITY_BILL, $model]);
                },
            ],
            [
                trans('texts.mark_paid'),
                function ($model) use ($entityType) {
                    return "javascript:submitForm_{$entityType}('markPaid', {$model->public_id})";
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_BILL && $model->bill_status_id != BILL_STATUS_PAID && Auth::user()->can('edit', [ENTITY_BILL, $model]);
                },
            ],
            [
                trans('texts.enter_payment'),
                function ($model) {
                    return URL::to("bill_payments/create/{$model->vendor_public_id}/{$model->public_id}");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_BILL && $model->bill_status_id != BILL_STATUS_PAID && Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.view_bill'),
                function ($model) {
                    return URL::to("bills/{$model->quote_bill_id}/edit");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_QUOTE && $model->quote_bill_id && Auth::user()->can('view', [ENTITY_BILL, $model]);
                },
            ],
            [
                trans('texts.new_proposal'),
                function ($model) {
                    return URL::to("proposals/create/{$model->public_id}");
                },
                function ($model) use ($entityType) {
                    return $entityType == ENTITY_QUOTE && !$model->quote_bill_id && $model->bill_status_id < BILL_STATUS_APPROVED && Auth::user()->can('create', ENTITY_PROPOSAL);
                },
            ],
        ];
    }

    private function getStatusLabel($model)
    {
        $class = Bill::calcStatusClass($model->bill_status_id, $model->balance, $model->partial_due_date ?: $model->due_date_sql, $model->is_recurring);
        $label = Bill::calcStatusLabel($model->bill_status_name, $class, $this->entityType, $model->quote_bill_id);

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }

    public function bulkActions()
    {
        $entityType = $this->entityType;

        $actions = [];

        if ($this->entityType == ENTITY_BILL || $this->entityType == ENTITY_BILL_QUOTE) {
            $actions[] = [
                'label' => mtrans($this->entityType, 'download_' . $this->entityType),
                'url' => 'javascript:submitForm_' . $this->entityType . '("download")',
            ];
            if (auth()->user()->isTrusted()) {
                $actions[] = [
                    'label' => mtrans($this->entityType, 'email_' . $this->entityType),
                    'url' => 'javascript:submitForm_' . $this->entityType . '("emailBill")',
                ];
            }
            $actions[] = DropdownButton::DIVIDER;
            $actions[] = [
                'label' => mtrans($this->entityType, 'mark_sent'),
                'url' => 'javascript:submitForm_' . $this->entityType . '("markSent")',
            ];
        }

        if ($this->entityType == ENTITY_BILL) {
            $actions[] = [
                'label' => mtrans($this->entityType, 'mark_paid'),
                'url' => 'javascript:submitForm_' . $this->entityType . '("markPaid")',
            ];
        }

        $actions[] = DropdownButton::DIVIDER;

        $actions = array_merge($actions, parent::bulkActions());

        return $actions;
    }
}
