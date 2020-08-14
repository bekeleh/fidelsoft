<?php

namespace App\Ninja\Presenters;

use App\Libraries\Skype\InvoiceCard;
use App\Libraries\Utils;
use Carbon\Carbon;
use DropdownButton;
use Illuminate\Support\Facades\Auth;
use stdClass;

class BillPresenter extends EntityPresenter
{
    public function vendor()
    {
        return $this->entity->vendor ? $this->entity->vendor->getDisplayName() : '';
    }

    public function user()
    {
        return $this->entity->user->getDisplayName();
    }

    public function amount()
    {
        $Bill = $this->entity;
        $account = $Bill->account;

        return $account->formatMoney($Bill->amount, $Bill->vendor);
    }

    public function balance()
    {
        $Bill = $this->entity;
        $account = $Bill->account;

        return $account->formatMoney($Bill->balance, $Bill->vendor);
    }

    public function paid()
    {
        $Bill = $this->entity;
        $account = $Bill->account;

        return $account->formatMoney($Bill->amount - $Bill->balance, $Bill->vendor);
    }

    public function partial()
    {
        $Bill = $this->entity;
        $account = $Bill->account;

        return $account->formatMoney($Bill->partial, $Bill->vendor);
    }

    public function requestedAmount()
    {
        $Bill = $this->entity;
        $account = $Bill->account;

        return $account->formatMoney($Bill->getRequestedAmount(), $Bill->vendor);
    }

    public function balanceDueLabel()
    {
        if ($this->entity->partial > 0) {
            return 'partial_due';
        } elseif ($this->entity->isType(INVOICE_TYPE_QUOTE)) {
            return 'total';
        } else {
            return 'balance_due';
        }
    }

    public function age()
    {
        $Bill = $this->entity;
        $dueDate = $Bill->partial_due_date ?: $Bill->due_date;

        if (!$dueDate || $dueDate === '0000-00-00') {
            return 0;
        }

        $date = Carbon::parse($dueDate);

        if ($date->isFuture()) {
            return 0;
        }

        return $date->diffInDays();
    }

    public function ageGroup()
    {
        $age = $this->age();

        if ($age > 120) {
            return 'age_group_120';
        } elseif ($age > 90) {
            return 'age_group_90';
        } elseif ($age > 60) {
            return 'age_group_60';
        } elseif ($age > 30) {
            return 'age_group_30';
        } else {
            return 'age_group_0';
        }
    }

    public function dueDateLabel()
    {
        if ($this->entity->isType(INVOICE_TYPE_STANDARD)) {
            return trans('texts.due_date');
        } else {
            return trans('texts.valid_until');
        }
    }

    public function discount()
    {
        $Bill = $this->entity;

        if ($Bill->is_amount_discount) {
            return $Bill->account->formatMoney($Bill->discount);
        } else {
            return $Bill->discount . '%';
        }
    }

    // https://schema.org/PaymentStatusType
    public function paymentStatus()
    {
        if (!$this->entity->balance) {
            return 'PaymentComplete';
        } elseif ($this->entity->isOverdue()) {
            return 'PaymentPastDue';
        } else {
            return 'PaymentDue';
        }
    }

    public function status()
    {
        if ($this->entity->is_deleted) {
            return trans('texts.deleted');
        } elseif ($this->entity->trashed()) {
            return trans('texts.archived');
        } elseif ($this->entity->is_recurring) {
            return trans('texts.active');
        } else {
            $status = $this->entity->invoice_status ? $this->entity->invoice_status->name : 'draft';
            $status = strtolower($status);

            return trans("texts.status_{$status}");
        }
    }

    public function invoice_date()
    {
        return Utils::fromSqlDate($this->entity->invoice_date);
    }

    public function due_date()
    {
        return Utils::fromSqlDate($this->entity->due_date);
    }

    public function partial_due_date()
    {
        return Utils::fromSqlDate($this->entity->partial_due_date);
    }

    public function frequency()
    {
        $frequency = $this->entity->frequency ? $this->entity->frequency->name : '';
        $frequency = strtolower($frequency);

        return trans('texts.freq_' . $frequency);
    }

    public function email()
    {
        $vendor = $this->entity->vendor;

        return $vendor->contacts->count() ? $vendor->contacts[0]->email : '';
    }

    public function autoBillEmailMessage()
    {
        $vendor = $this->entity->vendor;
        $paymentMethod = $vendor->defaultPaymentMethod();

        if (!$paymentMethod) {
            return false;
        }

        if ($paymentMethod->payment_type_id === PAYMENT_TYPE_ACH) {
            $paymentMethodString = trans('texts.auto_bill_payment_method_bank_transfer');
        } elseif ($paymentMethod->payment_type_id == PAYMENT_TYPE_PAYPAL) {
            $paymentMethodString = trans('texts.auto_bill_payment_method_paypal');
        } else {
            $paymentMethodString = trans('texts.auto_bill_payment_method_credit_card');
        }

        $data = [
            'payment_method' => $paymentMethodString,
            'due_date' => $this->due_date(),
        ];

        return trans('texts.auto_bill_notification', $data);
    }

    public function skypeBot()
    {
        return new InvoiceCard($this->entity);
    }

    public function rBits()
    {
        $properties = new stdClass();
        $properties->terms_text = $this->entity->terms;
        $properties->note = $this->entity->public_notes;
        $properties->itemized_receipt = [];

        foreach ($this->entity->invoice_items as $item) {
            $properties->itemized_receipt[] = $item->present()->rBits;
        }

        $data = new stdClass();
        $data->receive_time = time();
        $data->type = 'transaction_details';
        $data->source = 'user';
        $data->properties = $properties;

        return [$data];
    }

    public function moreActions()
    {
        $Bill = $this->entity;
        $entityType = $Bill->getEntityType();

        $actions = [
            ['url' => 'javascript:onCloneBillClick()', 'label' => trans("texts.clone_BILL")]
        ];

        if (Auth::user()->can('create', ENTITY_BILL_QUOTE)) {
            $actions[] = ['url' => 'javascript:onClonePurchaseQuoteClick()', 'label' => trans("texts.clone_bill_quote")];
        }

        $actions[] = ['url' => url("{$entityType}s/{$entityType}_history/{$Bill->public_id}"), 'label' => trans('texts.view_history')];
//     delivery note
        if ($entityType == ENTITY_BILL) {
            $actions[] = ['url' => url("bills/receive_note/{$Bill->public_id}"), 'label' => trans('texts.receive_note')];
        }
//    packing list
        if ($entityType == ENTITY_BILL) {
            $actions[] = ['url' => url("bills/packing_list/{$Bill->public_id}"), 'label' => trans('texts.packing_list')];
        }

//      Return purchase
        if ($entityType == ENTITY_BILL) {
            $actions[] = ['url' => url("bills/return_purchase/{$Bill->public_id}"), 'label' => trans('texts.return_purchase')];
        }

        $actions[] = DropdownButton::DIVIDER;

        if ($entityType == ENTITY_BILL_QUOTE) {
            if ($Bill->quote_invoice_id) {
                $actions[] = ['url' => url("bills/{$Bill->quote_invoice_id}/edit"), 'label' => trans('texts.view_invoice')];
            } else {
                if (!$Bill->isApproved()) {
                    $actions[] = ['url' => url("proposals/create/{$Bill->public_id}"), 'label' => trans('texts.new_proposal')];
                }
                $actions[] = ['url' => 'javascript:onConvertClick()', 'label' => trans('texts.convert_to_invoice')];
            }
        } elseif ($entityType == ENTITY_BILL) {
            if ($Bill->quote_id && $Bill->quote) {
                $actions[] = ['url' => url("bill_quotes/{$Bill->quote->public_id}/edit"), 'label' => trans('texts.view_quote')];
            }

            if ($Bill->onlyHasTasks()) {
                $actions[] = ['url' => 'javascript:onAddItemClick()', 'label' => trans('texts.add_product')];
            }

            if ($Bill->canBePaid()) {
                $actions[] = ['url' => 'javascript:submitBulkAction("markPaid")', 'label' => trans('texts.mark_paid')];
                $actions[] = ['url' => 'javascript:onPaymentClick()', 'label' => trans('texts.enter_payment')];
            }

            foreach ($Bill->payments as $payment) {
                $label = trans('texts.view_payment');
                if ($Bill->payments->count() > 1) {
                    $label .= ' - ' . $Bill->account->formatMoney($payment->amount, $Bill->vendor);
                }
                $actions[] = ['url' => $payment->present()->url, 'label' => $label];
            }
        }

        if (count($actions) > 3) {
            $actions[] = DropdownButton::DIVIDER;
        }

        if (!$Bill->trashed()) {
            $actions[] = ['url' => 'javascript:onArchiveClick()', 'label' => trans("texts.archive_{$entityType}")];
        }
        if (!$Bill->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_{$entityType}")];
        }

        return $actions;
    }

    public function gatewayFee($gatewayTypeId = false)
    {
        $Bill = $this->entity;
        $account = $Bill->account;

        if (!$account->gateway_fee_enabled) {
            return '';
        }

        $settings = $account->getGatewaySettings($gatewayTypeId);

        if (!$settings || !$settings->areFeesEnabled()) {
            return '';
        }

        if ($Bill->getGatewayFeeItem()) {
            $label = ' + ' . trans('texts.fee');
        } else {
            $fee = $Bill->calcGatewayFee($gatewayTypeId, true);
            $fee = $account->formatMoney($fee, $Bill->vendor);

            if (floatval($settings->fee_amount) < 0 || floatval($settings->fee_percent) < 0) {
                $label = trans('texts.discount');
            } else {
                $label = trans('texts.fee');
            }

            $label = ' - ' . $fee . ' ' . $label;
        }

        $label .= '&nbsp;&nbsp; <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="' . trans('texts.fee_help') . '"></i>';

        return $label;
    }

    public function multiAccountLink()
    {
        $Bill = $this->entity;
        $account = $Bill->account;

        if ($account->hasMultipleAccounts()) {
            $link = url(sprintf('/account/%s?redirect_to=%s', $account->account_key,
                $Bill->present()->path));
        } else {
            $link = $Bill->present()->url;
        }

        return $link;
    }

    public function calendarEvent($subColors = false)
    {
        $data = parent::calendarEvent();
        $Bill = $this->entity;
        $entityType = $Bill->getEntityType();

        $data->title = trans("texts.{$entityType}") . ' ' . $Bill->invoice_number . ' | ' . $this->amount() . ' | ' . $this->vendor();
        $data->start = $Bill->due_date ?: $Bill->invoice_date;

        if ($subColors) {
            $data->borderColor = $data->backgroundColor = $Bill->present()->statusColor();
        } else {
            $data->borderColor = $data->backgroundColor = $Bill->isQuote() ? '#716cb1' : '#377eb8';
        }

        return $data;
    }
}
