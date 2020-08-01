<?php

namespace App\Ninja\Presenters;

use App\Libraries\Utils;
use HTML;

class VendorPresenter extends EntityPresenter
{
    public function country()
    {
        return $this->entity->country ? $this->entity->country->getName() : false;
    }

    public function shipping_country()
    {
        return $this->entity->shipping_country ? $this->entity->shipping_country->getName() : false;
    }

    public function balance()
    {
        $vendor = $this->entity;
        $account = $vendor->account;

        return $account->formatMoney($vendor->balance, $vendor);
    }

    public function websiteLink()
    {
        $vendor = $this->entity;

        if (!$vendor->website) {
            return false;
        }

        $link = Utils::addHttp($vendor->website);

        return link_to($link, $vendor->website, ['target' => '_blank']);
    }

    public function paid_to_date()
    {
        $vendor = $this->entity;
        $account = $vendor->account;

        return $account->formatMoney($vendor->paid_to_date, $vendor);
    }

    public function paymentTerms()
    {
        $vendor = $this->entity;

        if (!$vendor->payment_terms) {
            return false;
        }

        return HTML::link('payment_terms/' . $vendor->id, $vendor->defaultDaysDue());
    }

    public function vendorType()
    {
        $vendor = $this->entity;

        if (!$vendor->vendorType) {
            return false;
        }

        return HTML::link('vendor_types/' . $vendor->vendorType->public_id, $vendor->vendorType->name);
    }

    public function holdReason()
    {
        $vendor = $this->entity;

        if (!$vendor->holdReason) {
            return false;
        }

        return HTML::link('hold_reasons/' . $vendor->holdReason->public_id, $vendor->holdReason->name);
    }

    public function address($addressType = ADDRESS_BILLING, $showHeader = false)
    {
        $str = false;
        $prefix = $addressType == ADDRESS_BILLING ? '' : 'shipping_';
        $vendor = $this->entity;

        if ($address1 = $vendor->{$prefix . 'address1'}) {
            $str .= e($address1) . '<br/>';
        }
        if ($address2 = $vendor->{$prefix . 'address2'}) {
            $str .= e($address2) . '<br/>';
        }
        if ($cityState = $this->getCityState($addressType)) {
            $str .= e($cityState) . '<br/>';
        }
        if ($country = $vendor->{$prefix . 'country'}) {
            $str .= e($country->getName()) . '<br/>';
        }

        if ($str && $showHeader) {
            $str = '<b>' . trans('texts.' . $addressType) . '</b><br/>' . $str;
        }

        return $str;
    }

    public function getCityState($addressType = ADDRESS_BILLING)
    {
        $vendor = $this->entity;
        $prefix = $addressType == ADDRESS_BILLING ? '' : 'shipping_';
        $swap = $vendor->{$prefix . 'country'} && $vendor->{$prefix . 'country'}->swap_postal_code;

        $city = e($vendor->{$prefix . 'city'});
        $state = e($vendor->{$prefix . 'state'});
        $postalCode = e($vendor->{$prefix . 'postal_code'});

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
            return false;
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
