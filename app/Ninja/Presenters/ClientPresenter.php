<?php

namespace App\Ninja\Presenters;

use App\Libraries\Utils;

class ClientPresenter extends EntityPresenter
{
    public function country()
    {
        return $this->entity->country ? $this->entity->country->getName() : '';
    }

    public function shipping_country()
    {
        return $this->entity->shipping_country ? $this->entity->shipping_country->getName() : '';
    }

    public function balance()
    {
        $client = $this->entity;
        $account = $client->account;

        return $account->formatMoney($client->balance, $client);
    }

    public function websiteLink()
    {
        $client = $this->entity;

        if (!$client->website) {
            return '';
        }

        $link = Utils::addHttp($client->website);

        return link_to($link, $client->website, ['target' => '_blank']);
    }

    public function paid_to_date()
    {
        $client = $this->entity;
        $account = $client->account;

        return $account->formatMoney($client->paid_to_date, $client);
    }

    public function paymentTerms()
    {
        $client = $this->entity;

        if (!$client->payment_terms) {
            return '';
        }

        return \HTML::link('payment_terms/' . $client->id, $client->defaultDaysDue());
    }

    public function saleType()
    {
        $client = $this->entity;

        if (!$client->saleType) {
            return '';
        }

        return \HTML::link('sale_types/' . $client->saleType->public_id, $client->saleType->name);
    }

    public function holdReason()
    {
        $client = $this->entity;

        if (!$client->holdReason) {
            return '';
        }

        return \HTML::link('hold_reasons/' . $client->holdReason->public_id, $client->holdReason->name);
    }

    public function address($addressType = ADDRESS_BILLING, $showHeader = false)
    {
        $str = '';
        $prefix = $addressType == ADDRESS_BILLING ? '' : 'shipping_';
        $client = $this->entity;

        if ($address1 = $client->{$prefix . 'address1'}) {
            $str .= e($address1) . '<br/>';
        }
        if ($address2 = $client->{$prefix . 'address2'}) {
            $str .= e($address2) . '<br/>';
        }
        if ($cityState = $this->getCityState($addressType)) {
            $str .= e($cityState) . '<br/>';
        }
        if ($country = $client->{$prefix . 'country'}) {
            $str .= e($country->getName()) . '<br/>';
        }

        if ($str && $showHeader) {
            $str = '<b>' . trans('texts.' . $addressType) . '</b><br/>' . $str;
        }

        return $str;
    }

    public function getCityState($addressType = ADDRESS_BILLING)
    {
        $client = $this->entity;
        $prefix = $addressType == ADDRESS_BILLING ? '' : 'shipping_';
        $swap = $client->{$prefix . 'country'} && $client->{$prefix . 'country'}->swap_postal_code;

        $city = e($client->{$prefix . 'city'});
        $state = e($client->{$prefix . 'state'});
        $postalCode = e($client->{$prefix . 'postal_code'});

        if ($city || $state || $postalCode) {
            return Utils::cityStateZip($city, $state, $postalCode, $swap);
        } else {
            return false;
        }
    }

    public function taskRate()
    {
        if (floatval($this->entity->task_rate)) {
            return Utils::roundSignificant($this->entity->task_rate);
        } else {
            return '';
        }
    }

    public function defaultTaskRate()
    {
        if ($rate = $this->taskRate()) {
            return $rate;
        } else {
            return $this->entity->account->present()->taskRate;
        }
    }

}
