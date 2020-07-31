<?php

namespace App\Models\Traits;

use App\Models\Vendor;
use App\Models\PurchaseInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class GenerateVendorNumbers.
 */
trait GenerateVendorNumbers
{

// get next number vendor
    public function getVendorNextNumber($entity = false)
    {
        $entity = $entity ?: new Vendor();
        $entityType = $entity->getEntityType();

        $counter = $this->getVendorCounter($entityType);
        $prefix = $this->getVendorNumberPrefix($entityType);
        $counterOffset = 0;
        $check = false;
        $lastNumber = false;

        if ($entityType == ENTITY_VENDOR && !$this->vendorNumbersEnabled()) {
            return false;
        }
        // confirm the purchase invoice number isn't already taken
        do {
            if ($this->hasIdPattern($entity)) {
                $number = $this->applyVendorNumberPattern($entity, $counter);
            } else {
                $number = $prefix . str_pad($counter, $this->invoice_number_padding, '0', STR_PAD_LEFT);
            }

            if ($entity->recurring_purchase_invoice_id) {
                $number = $this->recurring_purchase_invoice_number_prefix . $number;
            }

            if ($entity->isEntityType(ENTITY_VENDOR)) {
                $check = Vendor::scope(false, $this->id)->where('id_number', $number)->withTrashed()->first();
            } else {
                $check = PurchaseInvoice::scope(false, $this->id)->where('invoice_number', $number)->withTrashed()->first();
            }
            $counter++;
            $counterOffset++;

            // prevent getting stuck in a loop
            if ($number === $lastNumber) {
                return '';
            }

            $lastNumber = $number;

        } while ($check);

        // update the counter to be caught up
        if ($counterOffset > 1) {
            $this->syncOriginal();
            if ($entity->isEntityType(ENTITY_VENDOR)) {
                if ($this->vendorNumbersEnabled()) {
                    $this->vendor_number_counter += $counterOffset - 1;
                    $this->save();
                }
            } elseif ($entity->isEntityType(ENTITY_PURCHASE_CREDIT)) {
                if ($this->vendorNumbersEnabled()) {
                    $this->vendor_number_counter += $counterOffset - 1;
                    $this->save();
                }
            } elseif ($entity->isType(PURCHASE_INVOICE_TYPE_QUOTE)) {
                if (!$this->share_purchase_counter) {
                    $this->quote_number_counter += $counterOffset - 1;
                    $this->save();
                }
            } else {
                $this->invoice_number_counter += $counterOffset - 1;
                $this->save();
            }
        }

        return $number;
    }

    /**
     * @param $entityType
     *
     * @return string
     */
    public function getVendorNumberPrefix($entityType)
    {
        if (!$this->hasFeature(FEATURE_PURCHASE_INVOICE_SETTINGS)) {
            return '';
        }

        $field = "{$entityType}_number_prefix";

        return $this->$field ?: '';
    }

    public function getVendorNumberPattern($entityType)
    {
        if (!$this->hasFeature(FEATURE_PURCHASE_INVOICE_SETTINGS)) {
            return false;
        }

        $field = "{$entityType}_number_pattern";

        return $this->$field;
    }


    public function hasIdPattern($entityType)
    {
        return $this->hasVendorNumberPattern($entityType) ? true : false;
    }

    public function hasVendorNumberPattern($purchaseInvoice)
    {
        if (!$this->isPro()) {
            return false;
        }
        $pattern = false;
        if (isset($purchaseInvoice->invoice_type_id)) {
            $pattern = $purchaseInvoice->invoice_type_id == PURCHASE_INVOICE_TYPE_QUOTE ? $this->purchase_quote_number_pattern : $this->purchase_invoice_number_pattern;
        }
        return strstr($pattern, '$vendor') !== false || strstr($pattern, '$idNumber') !== false;
    }

    public function applyVendorNumberPattern($entity, $counter = 0)
    {
        $entityType = $entity->getEntityType();
        $counter = $counter ?: $this->getVendorCounter($entityType);
        $pattern = $this->getVendorNumberPattern($entityType);

        if (!$pattern) {
            return false;
        }

        $search = ['{$year}'];
        $replace = [date('Y')];

        $search[] = '{$counter}';
        $replace[] = str_pad($counter, $this->invoice_number_padding, '0', STR_PAD_LEFT);

        if (strstr($pattern, '{$userId}')) {
            $userId = $entity->user ? $entity->user->public_id : (Auth::check() ? Auth::user()->public_id : 0);
            $search[] = '{$userId}';
            $replace[] = str_pad(($userId + 1), 2, '0', STR_PAD_LEFT);
        }

        $matches = false;
        preg_match('/{\$date:(.*?)}/', $pattern, $matches);
        if (count($matches) > 1) {
            $format = $matches[1];
            $search[] = $matches[0];
            //$date = date_create()->format($format);
            $date = Carbon::now(session(SESSION_TIMEZONE, DEFAULT_TIMEZONE))->format($format);
            $replace[] = str_replace($format, $date, $matches[1]);
        }

        $pattern = str_replace($search, $replace, $pattern);
        $pattern = $this->getVendorPurchaseInvoiceNumber($pattern, $entity);

        return $pattern;
    }

    private function getVendorPurchaseInvoiceNumber($pattern, $purchaseInvoice)
    {
        if (!$purchaseInvoice->vendor_id) {
            return $pattern;
        }

        $search = [
            '{$custom1}',
            '{$custom2}',
            '{$idNumber}',
            '{$vendorCustom1}',
            '{$vendorCustom2}',
            '{$vendorIdNumber}',
            '{$vendorCounter}',
        ];

        $vendor = $purchaseInvoice->vendor;
        $vendorCounter = ($purchaseInvoice->isQuote() && !$this->share_counter) ? $vendor->quote_number_counter : $vendor->invoice_number_counter;

        $replace = [
            $vendor->custom_value1,
            $vendor->custom_value2,
            $vendor->id_number,
            $vendor->custom_value1, // backwards compatibility
            $vendor->custom_value2,
            $vendor->id_number,
            str_pad($vendorCounter, $this->invoice_number_padding, '0', STR_PAD_LEFT),
        ];

        return str_replace($search, $replace, $pattern);
    }

    public function getVendorCounter($entityType)
    {
        if ($entityType == ENTITY_VENDOR) {
            return $this->vendor_number_counter;
        } elseif ($entityType == ENTITY_PURCHASE_CREDIT) {
            return $this->vendor_credit_number_counter;
        } elseif ($entityType == ENTITY_PURCHASE_QUOTE && !$this->share_counter) {
            return $this->quote_number_counter;
        } else {
            return $this->invoice_number_counter;
        }
    }

    public function previewNextPurchaseInvoiceNumber($entityType = ENTITY_PURCHASE_INVOICE)
    {
        $vendor = Vendor::scope()->first();

        $purchaseInvoice = $this->createPurchaseInvoice($entityType, $vendor ? $vendor->id : 0);

        return $this->getVendorNextNumber($purchaseInvoice);
    }

    public function purchaseIncrementCounter($entity)
    {
        if ($entity->isEntityType(ENTITY_VENDOR)) {
            if ($this->vendor_number_counter > 0) {
                $this->vendor_number_counter += 1;
            }
            $this->save();
            return;
        } elseif ($entity->isEntityType(ENTITY_PURCHASE_CREDIT)) {
            if ($this->credit_number_counter > 0) {
                $this->credit_number_counter += 1;
            }
            $this->save();
            return;
        }

        if ($this->usesVendorInvoiceCounter()) {
            if ($entity->isType(PURCHASE_INVOICE_TYPE_QUOTE) && !$this->share_purchase_counter) {
                $entity->vendor->quote_number_counter += 1;
            } else {
                $entity->vendor->invoice_number_counter += 1;
            }
            $entity->vendor->save();
        }
//   purchase invoice counter
        if ($this->usesPurchaseInvoiceCounter()) {
            if ($entity->isType(PURCHASE_INVOICE_TYPE_QUOTE) && !$this->share_purchase_counter) {
                $this->quote_number_counter += 1;
            } else {
                $this->invoice_number_counter += 1;
            }
            $this->save();
        }
    }

    public function usesPurchaseInvoiceCounter()
    {
        return !$this->hasIdPattern(ENTITY_PURCHASE_INVOICE) || strpos($this->purchase_invoice_number_pattern, '{$counter}') !== false;
    }

    public function usesVendorInvoiceCounter()
    {
        return strpos($this->purchase_invoice_number_pattern, '{$vendorCounter}') !== false;
    }

    public function vendorNumbersEnabled()
    {
        return $this->hasFeature(FEATURE_PURCHASE_INVOICE_SETTINGS) && $this->vendor_number_counter > 0;
    }

    public function checkPurchaseCounterReset()
    {
        if (!$this->reset_purchase_counter_frequency_id || !$this->reset_purchase_counter_date) {
            return false;
        }

        $timezone = $this->getTimezone();
        $resetDate = Carbon::parse($this->reset_purchase_counter_date, $timezone);

        if (!$resetDate->isToday()) {
            return false;
        }

        switch ($this->reset_purchase_counter_frequency_id) {
            case FREQUENCY_WEEKLY:
                $resetDate->addWeek();
                break;
            case FREQUENCY_TWO_WEEKS:
                $resetDate->addWeeks(2);
                break;
            case FREQUENCY_FOUR_WEEKS:
                $resetDate->addWeeks(4);
                break;
            case FREQUENCY_MONTHLY:
                $resetDate->addMonth();
                break;
            case FREQUENCY_TWO_MONTHS:
                $resetDate->addMonths(2);
                break;
            case FREQUENCY_THREE_MONTHS:
                $resetDate->addMonths(3);
                break;
            case FREQUENCY_FOUR_MONTHS:
                $resetDate->addMonths(4);
                break;
            case FREQUENCY_SIX_MONTHS:
                $resetDate->addMonths(6);
                break;
            case FREQUENCY_ANNUALLY:
                $resetDate->addYear();
                break;
            case FREQUENCY_TWO_YEARS:
                $resetDate->addYears(2);
                break;
        }

        $this->reset_purchase_counter_date = $resetDate->format('Y-m-d');
        $this->invoice_number_counter = 1;
        $this->quote_number_counter = 1;
        $this->vendor_number_counter = $this->vendor_number_counter > 0 ? 1 : 0;

        $this->save();
    }
}
