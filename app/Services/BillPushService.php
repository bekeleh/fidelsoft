<?php

namespace App\Services;

use App\Models\Common\Account;
use App\Models\Bill;
use App\Ninja\Notifications\PushFactory;

/**
 * Class PushService.
 */
class BillPushService
{

    protected $pushFactory;

    public function __construct(PushFactory $pushFactory)
    {
        $this->pushFactory = $pushFactory;
    }

    public function sendNotification(Bill $bill, $type)
    {
        //check user has registered for push notifications
        if (!$this->checkDeviceExists($bill->account)) {
            return;
        }

        //Harvest an array of devices that are registered for this notification type
        $devices = json_decode($bill->account->devices, true);

        foreach ($devices as $device) {
            if (($device["notify_{$type}"] == true) && ($device['device'] == 'ios') && IOS_DEVICE) {
                $this->pushMessage($bill, $device['token'], $type, IOS_DEVICE);
            } elseif (($device["notify_{$type}"] == true) && ($device['device'] == 'fcm') && ANDROID_DEVICE) {
                $this->pushMessage($bill, $device['token'], $type, ANDROID_DEVICE);
            }
        }
    }

    private function pushMessage(Bill $bill, $token, $type, $device)
    {
        $this->pushFactory->message($token, $this->messageType($bill, $type), $device);
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

    private function messageType(Bill $bill, $type)
    {
        switch ($type) {
            case 'sent':
                return $this->entitySentMessage($bill);
                break;

            case 'paid':
                return $this->billPaidMessage($bill);
                break;

            case 'approved':
                return $this->quoteApprovedMessage($bill);
                break;

            case 'viewed':
                return $this->entityViewedMessage($bill);
                break;
        }
    }

    private function entitySentMessage(Bill $bill)
    {
        if ($bill->isType(BILL_TYPE_QUOTE)) {
            return trans('texts.notification_bill_quote_sent_subject', ['bill' => $bill->bill_number, 'vendor' => $bill->vendor->name]);
        } else {
            return trans('texts.notification_bill_sent_subject', ['bill' => $bill->bill_number, 'vendor' => $bill->vendor->name]);
        }
    }

    private function billPaidMessage(Bill $bill)
    {
        return trans('texts.notification_bill_paid_subject', ['bill' => $bill->bill_number, 'vendor' => $bill->vendor->name]);
    }

    private function quoteApprovedMessage(Bill $bill)
    {
        return trans('texts.notification_bill_quote_approved_subject', ['bill' => $bill->bill_number, 'vendor' => $bill->vendor->name]);
    }

    private function entityViewedMessage(Bill $bill)
    {
        if ($bill->isType(BILL_TYPE_QUOTE)) {
            return trans('texts.notification_bill_quote_viewed_subject', ['bill' => $bill->bill_number, 'vendor' => $bill->vendor->name]);
        } else {
            return trans('texts.notification_bill_viewed_subject', ['bill' => $bill->bill_number, 'vendor' => $bill->vendor->name]);
        }
    }
}
