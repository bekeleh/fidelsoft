<?php

namespace App\Ninja\Presenters;

use App\Libraries\Skype\BillCard;
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
        $bill = $this->entity;
        $account = $bill->account;

        return $account->formatMoney($bill->amount, $bill->vendor);
    }

    public function balance()
    {
        $bill = $this->entity;
        $account = $bill->account;

        return $account->formatMoney($bill->balance, $bill->vendor);
    }

    public function paid()
    {
        $bill = $this->entity;
        $account = $bill->account;

        return $account->formatMoney($bill->amount - $bill->balance, $bill->vendor);
    }

    public function partial()
    {
        $bill = $this->entity;
        $account = $bill->account;

        return $account->formatMoney($bill->partial, $bill->vendor);
    }

    public function requestedAmount()
    {
        $bill = $this->entity;
        $account = $bill->account;

        return $account->formatMoney($bill->getRequestedAmount(), $bill->vendor);
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
        $bill = $this->entity;
        $dueDate = $bill->partial_due_date ?: $bill->due_date;

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
        $bill = $this->entity;

        if ($bill->is_amount_discount) {
            return $bill->account->formatMoney($bill->discount);
        } else {
            return $bill->discount . '%';
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

    public function bill_date()
    {
        return Utils::fromSqlDate($this->entity->bill_date);
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
        $billPaymentMethod = $vendor->defaultPaymentMethod();

        if (!$billPaymentMethod) {
            return false;
        }

        if ($billPaymentMethod->payment_type_id === PAYMENT_TYPE_ACH) {
            $billPaymentMethodString = trans('texts.auto_bill_payment_method_bank_transfer');
        } elseif ($billPaymentMethod->payment_type_id == PAYMENT_TYPE_PAYPAL) {
            $billPaymentMethodString = trans('texts.auto_bill_payment_method_paypal');
        } else {
            $billPaymentMethodString = trans('texts.auto_bill_payment_method_credit_card');
        }

        $data = [
            'payment_method' => $billPaymentMethodString,
            'due_date' => $this->due_date(),
        ];

        return trans('texts.auto_bill_notification', $data);
    }

    public function skypeBot()
    {
        return new BillCard($this->entity);
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
        $bill = $this->entity;
        $entityType = $bill->getEntityType();

        $actions = [
            ['url' => 'javascript:onCloneBillClick()', 'label' => trans("texts.clone_bill")]
        ];

        if (Auth::user()->can('create', ENTITY_BILL_QUOTE)) {
            $actions[] = ['url' => 'javascript:onCloneBillQuoteClick()', 'label' => trans("texts.clone_bill_quote")];
        }

        $actions[] = ['url' => url("{$entityType}s/{$entityType}_history/{$bill->public_id}"), 'label' => trans('texts.view_history')];
//     delivery note
        if ($entityType == ENTITY_BILL) {
            $actions[] = ['url' => url("bills/receive_note/{$bill->public_id}"), 'label' => trans('texts.receive_note')];
        }
//    packing list
        if ($entityType == ENTITY_BILL) {
            $actions[] = ['url' => url("bills/packing_list/{$bill->public_id}"), 'label' => trans('texts.packing_list')];
        }

//      Return purchase Bill
        if ($entityType == ENTITY_BILL) {
            $actions[] = ['url' => url("bills/return_bill/{$bill->public_id}"), 'label' => trans('texts.return_bill')];
        }

        $actions[] = DropdownButton::DIVIDER;

        if ($entityType == ENTITY_BILL_QUOTE) {
            if ($bill->quote_bill_id) {
                $actions[] = ['url' => url("bills/{$bill->quote_bill_id}/edit"), 'label' => trans('texts.view_bill')];
            } else {
                if (!$bill->isApproved()) {
                    $actions[] = ['url' => url("proposals/create/{$bill->public_id}"), 'label' => trans('texts.new_proposal')];
                }
                $actions[] = ['url' => 'javascript:onConvertClick()', 'label' => trans('texts.convert_to_bill')];
            }
        } elseif ($entityType == ENTITY_BILL) {
            if ($bill->quote_id && $bill->quote) {
                $actions[] = ['url' => url("bill_quotes/{$bill->quote->public_id}/edit"), 'label' => trans('texts.view_quote')];
            }

            if ($bill->onlyHasTasks()) {
                $actions[] = ['url' => 'javascript:onAddItemClick()', 'label' => trans('texts.add_product')];
            }

            if ($bill->canBePaid()) {
                $actions[] = ['url' => 'javascript:submitBulkAction("markPaid")', 'label' => trans('texts.mark_paid')];
                $actions[] = ['url' => 'javascript:onPaymentClick()', 'label' => trans('texts.enter_payment')];
            }

            foreach ($bill->bill_payments as $billPayment) {
                $label = trans('texts.view_payment');
                if ($bill->bill_payments->count() > 1) {
                    $label .= ' - ' . $bill->account->formatMoney($billPayment->amount, $bill->vendor);
                }

                $actions[] = ['url' => $billPayment->present()->url, 'label' => $label];
            }
        }

        if (count($actions) > 3) {
            $actions[] = DropdownButton::DIVIDER;
        }

        if (!$bill->trashed()) {
            $actions[] = ['url' => 'javascript:onArchiveClick()', 'label' => trans("texts.archive_{$entityType}")];
        }
        if (!$bill->is_deleted) {
            $actions[] = ['url' => 'javascript:onDeleteClick()', 'label' => trans("texts.delete_{$entityType}")];
        }

        return $actions;
    }

    public function gatewayFee($gatewayTypeId = false)
    {
        $bill = $this->entity;
        $account = $bill->account;

        if (!$account->gateway_fee_enabled) {
            return '';
        }

        $settings = $account->getGatewaySettings($gatewayTypeId);

        if (!$settings || !$settings->areFeesEnabled()) {
            return '';
        }

        if ($bill->getGatewayFeeItem()) {
            $label = ' + ' . trans('texts.fee');
        } else {
            $fee = $bill->calcGatewayFee($gatewayTypeId, true);
            $fee = $account->formatMoney($fee, $bill->vendor);

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
        $bill = $this->entity;
        $account = $bill->account;

        if ($account->hasMultipleAccounts()) {
            $link = url(sprintf('/account/%s?redirect_to=%s', $account->account_key,
                $bill->present()->path));
        } else {
            $link = $bill->present()->url;
        }

        return $link;
    }

    public function calendarEvent($subColors = false)
    {
        $data = parent::calendarEvent();
        $bill = $this->entity;
        $entityType = $bill->getEntityType();

        $data->title = trans("texts.{$entityType}") . ' ' . $bill->bill_number . ' | ' . $this->amount() . ' | ' . $this->vendor();
        $data->start = $bill->due_date ?: $bill->bill_date;

        if ($subColors) {
            $data->borderColor = $data->backgroundColor = $bill->present()->statusColor();
        } else {
            $data->borderColor = $data->backgroundColor = $bill->isQuote() ? '#716cb1' : '#377eb8';
        }

        return $data;
    }
}
