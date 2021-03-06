<?php

namespace App\Ninja\Mailers;

use App\Models\BillInvitation;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use Log;

class BillMailer extends BillSender
{
    public function sendConfirmation(User $user, User $invitor = null)
    {
        if (!$user->email) {
            return;
        }

        $view = 'confirm';
        $subject = trans('texts.confirmation_subject');

        $data = [
            'user' => $user,
            'invitationMessage' => $invitor ? trans('texts.invitation_message', ['invitor' => $invitor->getDisplayName()]) : '',
        ];

        if ($invitor) {
            $fromEmail = $invitor->email;
            $fromName = $invitor->getDisplayName();
        } else {
            $fromEmail = CONTACT_EMAIL;
            $fromName = CONTACT_NAME;
        }

        $this->sendTo($user->email, $fromEmail, $fromName, $subject, $view, $data);
    }

    public function sendEmailChanged(User $user)
    {
        $oldEmail = $user->getOriginal('email');
        $newEmail = $user->email;

        if (!$oldEmail || !$newEmail) {
            return;
        }

        $view = 'user_message';
        $subject = trans('texts.email_address_changed');

        $data = [
            'user' => $user,
            'userName' => $user->getDisplayName(),
            'primaryMessage' => trans('texts.email_address_changed_message', ['old_email' => $oldEmail, 'new_email' => $newEmail]),
        ];

        $this->sendTo($oldEmail, CONTACT_EMAIL, CONTACT_NAME, $subject, $view, $data);
    }

    public function sendNotification(User $user, Bill $bill, $notificationType, BillPayment $payment = null, $notes = false)
    {
        if (!$user->shouldNotifyBill($bill)) {
            return;
        }

        $entityType = $bill->getEntityType();
        $view = ($notificationType == 'approved' ? ENTITY_BILL_QUOTE : ENTITY_BILL) . "_{$notificationType}";
        $account = $user->account;
        $vendor = $bill->vendor;
        $link = $bill->present()->multiAccountLink;

        $data = [
            'entityType' => $entityType,
            'vendorName' => $vendor->getDisplayName(),
            'accountName' => $account->getDisplayName(),
            'userName' => $user->getDisplayName(),
            'billAmount' => $account->formatMoney($bill->getRequestedAmount(), $vendor),
            'billNumber' => $bill->invoice_number,
            'billLink' => $link,
            'account' => $account,
        ];

        if ($payment) {
            $data['payment'] = $payment;
            $data['paymentAmount'] = $account->formatMoney($payment->amount, $vendor);
        }

        $subject = trans("texts.notification_{$entityType}_{$notificationType}_subject", [
            'bill' => $bill->invoice_number,
            'vendor' => $vendor->getDisplayName(),
        ]);

        if ($notes) {
            $subject .= ' [' . trans('texts.notes_' . $notes) . ']';
        }

        $this->sendTo($user->email, CONTACT_EMAIL, CONTACT_NAME, $subject, $view, $data);
    }

    public function sendEmailBounced(BillInvitation $invitation)
    {
        $user = $invitation->user;
        $account = $user->account;
        $bill = $invitation->bill;
        $entityType = $bill->getEntityType();

        if (!$user->email) {
            return;
        }

        $subject = trans("texts.notification_{$entityType}_bounced_subject", ['bill' => $bill->invoice_number]);
        $view = 'email_bounced';
        $data = [
            'userName' => $user->getDisplayName(),
            'emailError' => $invitation->email_error,
            'entityType' => $entityType,
            'contactName' => $invitation->contact->getDisplayName(),
            'billNumber' => $bill->invoice_number,
        ];

        $this->sendTo($user->email, CONTACT_EMAIL, CONTACT_NAME, $subject, $view, $data);
    }

    public function sendMessage($user, $subject, $message, $data = false)
    {
        if (!$user->email) {
            return;
        }

        $bill = $data && isset($data['bill']) ? $data['bill'] : false;
        $view = 'user_message';

        $data = $data ?: [];
        $data += [
            'userName' => $user->getDisplayName(),
            'primaryMessage' => $message,
            //'secondaryMessage' => $message,
            'billLink' => $bill ? $bill->present()->multiAccountLink : false,
        ];

        $this->sendTo($user->email, CONTACT_EMAIL, CONTACT_NAME, $subject, $view, $data);
    }

    public function sendSecurityCode($user, $code)
    {
        if (!$user->email) {
            return;
        }

        $subject = trans('texts.security_code_email_subject');
        $view = 'security_code';
        $data = [
            'userName' => $user->getDisplayName(),
            'code' => $code,
        ];

        $this->sendTo($user->email, CONTACT_EMAIL, CONTACT_NAME, $subject, $view, $data);
    }

    public function sendPasswordReset($user, $token)
    {
        if (!$user->email) {
            return;
        }

        $subject = trans('texts.your_password_reset_link');
        $view = 'password';
        $data = [
            'token' => $token,
        ];

        $this->sendTo($user->email, CONTACT_EMAIL, CONTACT_NAME, $subject, $view, $data);
    }

    public function sendScheduledReport($scheduledReport, $file)
    {
        $user = $scheduledReport->user;
        $config = json_decode($scheduledReport->config);

        if (!$user->email) {
            return;
        }

        $subject = sprintf('%s - %s %s', APP_NAME, trans('texts.' . $config->report_type), trans('texts.report'));
        $view = 'user_message';
        $data = [
            'userName' => $user->getDisplayName(),
            'primaryMessage' => trans('texts.scheduled_report_attached', ['type' => trans('texts.' . $config->report_type)]),
            'documents' => [[
                'name' => $file->filename . '.' . $config->export_format,
                'data' => $file->string($config->export_format),
            ]]
        ];

        $this->sendTo($user->email, CONTACT_EMAIL, CONTACT_NAME, $subject, $view, $data);
    }
}
