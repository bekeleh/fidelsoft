<?php

namespace App\Http\Mixins;

use App\Libraries\Utils;
use App\Models\Client;
use Carbon\Carbon;

class RulesMixin
{

    public function positive()
    {
        return function ($attribute, $value, $parameters) {
            return Utils::parseFloat($value) >= 0;
        };
    }

    public function has_credit()
    {
        return function ($attribute, $value, $parameters) {
            $publicClientId = $parameters[0];
            $amount = $parameters[1];

            $client = Client::scope($publicClientId)->firstOrFail();
            $credit = $client->getTotalCredit();

            return $credit >= $amount;
        };
    }

    public function time_log()
    {
        return function ($attribute, $value, $parameters) {
            $lastTime = 0;
            $value = json_decode($value);
            array_multisort($value);
            foreach ($value as $timeLog) {
                list($startTime, $endTime) = $timeLog;
                if (!$endTime) {
                    continue;
                }
                if ($startTime < $lastTime || $startTime > $endTime) {
                    return false;
                }
                if ($endTime < min($startTime, $lastTime)) {
                    return false;
                }
                $lastTime = max($lastTime, $endTime);
            }

            return true;
        };
    }

    public function has_counter()
    {
        return function ($attribute, $value, $parameters) {
            if (!$value) {
                return true;
            }

            if (strstr($value, '{$counter}') !== false) {
                return true;
            }

            return ((strstr($value, '{$idNumber}') !== false || strstr($value, '{$clientIdNumber}') != false) && (strstr($value, '{$clientCounter}')));
        };
    }

    public function valid_invoice_items()
    {
        return function ($attribute, $value, $parameters) {
            $total = 0;
            foreach ($value as $item) {
                $qty = !empty($item['qty']) ? Utils::parseFloat($item['qty']) : 1;
                $cost = !empty($item['cost']) ? Utils::parseFloat($item['cost']) : 1;
                $total += ($qty * $cost);
            }

            return $total <= MAX_INVOICE_AMOUNT;
        };
    }

    public function valid_subdomain()
    {
        return function ($attribute, $value, $parameters) {
            return !in_array($value, ['www', 'app', 'mail', 'admin', 'blog', 'user', 'contact', 'payment', 'payments', 'billing', 'invoice', 'business', 'owner', 'info', 'ninja', 'docs', 'doc', 'documents', 'download']);
        };
    }

    public function before()
    {
        return function (Carbon $date) {
            return 'before:' . $date->toDateTimeString();
        };
    }

    public function beforeOrEqual()
    {
        return function (Carbon $date) {
            return 'before_or_equal:' . $date->toDateTimeString();
        };
    }

    public function after()
    {
        return function (Carbon $date) {
            return 'after:' . $date->toDateTimeString();
        };
    }

    public function afterOrEqual()
    {
        return function (Carbon $date) {
            return 'after_or_equal:' . $date->toDateTimeString();
        };
    }
}