<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * TranslationComposer.php.
 *
 * @copyright See LICENSE file that was distributed with this source code.
 */
class TranslationComposer
{

    public function compose(View $view)
    {
        $view->with('industries', Cache::get('industries')->each(function ($industry) {
            $industry->name = trans('texts.industry_' . $industry->name);
        })->sortBy(function ($industry) {
            return $industry->name;
        }));

        $view->with('countries', Cache::get('countries')->each(function ($country) {
            $country->name = trans('texts.country_' . $country->name);
        })->sortBy(function ($country) {
            return $country->name;
        }));

        $view->with('paymentTypes', Cache::get('paymentTypes')->each(function ($pType) {
            $pType->name = trans('texts.payment_type_' . $pType->name);
        })->sortBy(function ($pType) {
            return $pType->name;
        }));

        $view->with('languages', Cache::get('languages')->each(function ($lang) {
            $lang->name = trans('texts.lang_' . $lang->name);
        })->sortBy(function ($lang) {
            return $lang->name;
        }));

        $view->with('currencies', Cache::get('currencies')->each(function ($currency) {
            $currency->name = trans('texts.currency_' . Str::slug($currency->name, '_'));
        })->sortBy(function ($currency) {
            return $currency->name;
        }));

        $view->with('units', Cache::get('units')->each(function ($unit) {
            $unit->name = trans('texts.unit_' . Str::slug($unit->name, '_'));
        })->sortBy(function ($unit) {
            return $unit->name;
        }));

        $view->with('categories', Cache::get('categories')->each(function ($category) {
            $category->name = trans('texts.category_' . Str::slug($category->name, '_'));
        })->sortBy(function ($category) {
            return $category->name;
        }));

        $view->with('taxCategories', Cache::get('taxCategories')->each(function ($taxCategory) {
            $taxCategory->name = trans('texts.tax_category_' . Str::slug($taxCategory->name, '_'));
        })->sortBy(function ($taxCategory) {
            return $taxCategory->name;
        }));

        $view->with('plans', Cache::get('plans')->each(function ($plan) {
            $plan->name = trans('texts.plan_' . Str::slug($plan->name, '_'));
        })->sortBy(function ($plan) {
            return $plan->name;
        }));

        $view->with('clientTypes', Cache::get('clientTypes')->each(function ($clientType) {
            $clientType->name = trans('texts.client_type_' . Str::slug($clientType->name, '_'));
        })->sortBy(function ($clientType) {
            return $clientType->name;
        }));

        $view->with('saleTypes', Cache::get('saleTypes')->each(function ($saleType) {
            $saleType->name = trans('texts.sale_type_' . Str::slug($saleType->name, '_'));
        })->sortBy(function ($saleType) {
            return $saleType->name;
        }));

        $view->with('holdReasons', Cache::get('holdReasons')->each(function ($holdReason) {
            $holdReason->name = trans('texts.hold_reason_' . Str::slug($holdReason->name, '_'));
        })->sortBy(function ($holdReason) {
            return $holdReason->name;
        }));

    }
}
