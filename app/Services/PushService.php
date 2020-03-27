<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Invoice;
use App\Ninja\Notifications\PushFactory;

/**
 * Class PushService.
 */
class PushService
{

    protected $pushFactory;

    public function __construct(PushFactory $pushFactory)
    {
        $this->pushFactory = $pushFactory;
    }

    public function sendNotification(Invoice $invoice, $type)
    {
        //check user has registered for push notifications
        if (!$this->checkDeviceExists($invoice->account)) {
            return;
        }

        //Harvest an array of devices that are registered for this notification type
        $devices = json_decode($invoice->account->devices, true);

        foreach ($devices as $device) {
            if (($device["notify_{$type}"] == true) && ($device['device'] == 'ios') && IOS_DEVICE) {
                $this->pushMessage($invoice, $device['token'], $type, IOS_DEVICE);
            } elseif (($device["notify_{$type}"] == true) && ($device['device'] == 'fcm') && ANDROID_DEVICE) {
                $this->pushMessage($invoice, $device['token'], $type, ANDROID_DEVICE);
            }
        }
    }

    private function pushMessage(Invoice $invoice, $token, $type, $device)
    {
        $this->pushFactory->message($token, $this->messageType($invoice, $type), $device);
    }

    private function checkDeviceExists(Account $account)
    {
        $devices = json_decode($account->devices, true);

        if (count((array)$devices) >= 1) {
            return true;
        } else {
            return false;
        }
    }

    private function messageType(Invoice $invoice, $type)
    {
        switch ($type) {
            case 'sent':
                return $this->entitySentMessage($invoice);
                break;

            case 'paid':
                return $this->invoicePaidMessage($invoice);
                break;

            case 'approved':
                return $this->quoteApprovedMessage($invoice);
                break;

            case 'viewed':
                return $this->entityViewedMessage($invoice);
                break;
        }
    }

    private function entitySentMessage(Invoice $invoice)
    {
        if ($invoice->isType(INVOICE_TYPE_QUOTE)) {
            return trans('texts.notification_quote_sent_subject', ['invoice' => $invoice->invoice_number, 'client' => $invoice->client->name]);
        } else {
            return trans('texts.notification_invoice_sent_subject', ['invoice' => $invoice->invoice_number, 'client' => $invoice->client->name]);
        }
    }

    private function invoicePaidMessage(Invoice $invoice)
    {
        return trans('texts.notification_invoice_paid_subject', ['invoice' => $invoice->invoice_number, 'client' => $invoice->client->name]);
    }

    private function quoteApprovedMessage(Invoice $invoice)
    {
        return trans('texts.notification_quote_approved_subject', ['invoice' => $invoice->invoice_number, 'client' => $invoice->client->name]);
    }

    private function entityViewedMessage(Invoice $invoice)
    {
        if ($invoice->isType(INVOICE_TYPE_QUOTE)) {
            return trans('texts.notification_quote_viewed_subject', ['invoice' => $invoice->invoice_number, 'client' => $invoice->client->name]);
        } else {
            return trans('texts.notification_invoice_viewed_subject', ['invoice' => $invoice->invoice_number, 'client' => $invoice->client->name]);
        }
    }
}
