<?php

namespace App\Models\Traits;

use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class GenerateClientNumbers.
 */
trait GenerateClientNumbers
{

// get client next number
    public function getClientNextNumber($entity = false)
    {
        $entity = $entity ?: new Client();
        $entityType = $entity->getEntityType();

        $counter = $this->getClientCounter($entityType);
        $prefix = $this->getClientNumberPrefix($entityType);
        $counterOffset = 0;
        $check = false;
        $lastNumber = false;

        if ($entityType == ENTITY_CLIENT && !$this->clientNumbersEnabled()) {
            return false;
        }

        // confirm the invoice number isn't already taken
        do {
            if ($this->hasClientIdentificationPattern($entityType)) {
                $number = $this->applyClientNumberPattern($entity, $counter);
            } else {
                $number = $prefix . str_pad($counter, $this->invoice_number_padding, '0', STR_PAD_LEFT);
            }

            if ($entity->recurring_invoice_id) {
                $number = $this->recurring_invoice_number_prefix . $number;
            }

            if ($entity->isEntityType(ENTITY_CLIENT)) {
                $check = Client::scope(false, $this->id)->where('id_number', $number)->withTrashed()->first();
            } else {
                $check = Invoice::scope(false, $this->id)->where('invoice_number', $number)->withTrashed()->first();
            }
            $counter++;
            $counterOffset++;

            // prevent getting stuck in a loop
            if ($number == $lastNumber) {
                return '';
            }
            $lastNumber = $number;

        } while ($check);

        // update the counter to be caught up
        if ($counterOffset > 1) {
            $this->syncOriginal();
            if ($entity->isEntityType(ENTITY_CLIENT)) {
                if ($this->clientNumbersEnabled()) {
                    $this->client_number_counter += $counterOffset - 1;
                    $this->save();
                }
            } elseif ($entity->isEntityType(ENTITY_CREDIT)) {
                if ($this->clientCreditNumbersEnabled()) {
                    $this->credit_number_counter += $counterOffset - 1;
                    $this->save();
                }
            } elseif ($entity->isType(INVOICE_TYPE_QUOTE)) {
                if (!$this->share_counter) {
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
    public function getClientNumberPrefix($entityType)
    {
        if (!$this->hasFeature(FEATURE_INVOICE_SETTINGS)) {
            return false;
        }

        $field = "{$entityType}_number_prefix";

        return $this->$field ?: '';
    }

    public function getClientNumberPattern($entityType)
    {
        if (!$this->hasFeature(FEATURE_INVOICE_SETTINGS)) {
            return false;
        }

        $field = "{$entityType}_number_pattern";

        return $this->$field ?: '';
    }

    public function hasClientIdentificationPattern($entityType)
    {
        return $this->getClientNumberPattern($entityType) ? true : false;
    }

    public function hasClientNumberPattern($invoice)
    {
        if (!$this->isPro()) {
            return false;
        }

        $pattern = $invoice->invoice_type_id == INVOICE_TYPE_QUOTE ? $this->quote_number_pattern : $this->invoice_number_pattern;

        return strstr($pattern, '$client') !== false || strstr($pattern, '$idNumber') !== false;
    }

    public function applyClientNumberPattern($entity, $counter = 0)
    {
        $entityType = $entity->getEntityType();
        $counter = $counter ?: $this->getClientCounter($entityType);
        $pattern = $this->getClientNumberPattern($entityType);

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
        $pattern = $this->getClientInvoiceNumber($pattern, $entity);

        return $pattern;
    }

    private function getClientInvoiceNumber($pattern, $invoice)
    {
        if (!$invoice->client_id) {
            return $pattern;
        }

        $search = [
            '{$custom1}',
            '{$custom2}',
            '{$idNumber}',
            '{$clientCustom1}',
            '{$clientCustom2}',
            '{$clientIdNumber}',
            '{$clientCounter}',
        ];

        $client = $invoice->client;
        $clientCounter = ($invoice->isQuote() && !$this->share_counter) ? $client->quote_number_counter : $client->invoice_number_counter;

        $replace = [
            $client->custom_value1,
            $client->custom_value2,
            $client->id_number,
            $client->custom_value1, // backwards compatibility
            $client->custom_value2,
            $client->id_number,
            str_pad($clientCounter, $this->invoice_number_padding, '0', STR_PAD_LEFT),
        ];

        return str_replace($search, $replace, $pattern);
    }

    public function getClientCounter($entityType)
    {
        if ($entityType == ENTITY_CLIENT) {
            return $this->client_number_counter;
        } elseif ($entityType == ENTITY_CREDIT) {
            return $this->credit_number_counter;
        } elseif ($entityType == ENTITY_QUOTE && !$this->share_counter) {
            return $this->quote_number_counter;
        } else {
            return $this->invoice_number_counter;
        }
    }

    public function previewNextInvoiceNumber($entityType = ENTITY_INVOICE)
    {
        $client = Client::scope()->first();

        $invoice = $this->createInvoice($entityType, $client ? $client->id : 0);

        return $this->getClientNextNumber($invoice);
    }

    public function clientIncrementCounter($entity)
    {
        if ($entity->isEntityType(ENTITY_CLIENT)) {
            if ($this->client_number_counter > 0) {
                $this->client_number_counter += 1;
            }
            $this->save();
            return true;
        } elseif ($entity->isEntityType(ENTITY_CREDIT)) {
            if ($this->credit_number_counter > 0) {
                $this->credit_number_counter += 1;
            }
            $this->save();
            return true;
        }

        if ($this->usesClientInvoiceCounter()) {
            if ($entity->isType(INVOICE_TYPE_QUOTE) && !$this->share_counter) {
                $entity->client->quote_number_counter += 1;
            } else {
                $entity->client->invoice_number_counter += 1;
            }

            $entity->client->save();
        }
        if ($this->usesInvoiceCounter()) {
            if ($entity->isType(INVOICE_TYPE_QUOTE) && !$this->share_counter) {
                $this->quote_number_counter += 1;
            } else {
                $this->invoice_number_counter += 1;
            }
            $this->save();
        }

        return true;
    }

    public function usesInvoiceCounter()
    {
        return !$this->hasClientIdentificationPattern(ENTITY_INVOICE) || strpos($this->invoice_number_pattern, '{$counter}') !== false;
    }

    public function usesClientInvoiceCounter()
    {
        return strpos($this->invoice_number_pattern, '{$clientCounter}') !== false;
    }

    public function clientNumbersEnabled()
    {
        return $this->hasFeature(FEATURE_INVOICE_SETTINGS) && $this->client_number_counter > 0;
    }

    public function clientCreditNumbersEnabled()
    {
        return $this->hasFeature(FEATURE_INVOICE_SETTINGS) && $this->credit_number_counter > 0;
    }

    public function checkInvoiceCounterReset()
    {
        if (!$this->reset_counter_frequency_id || !$this->reset_counter_date) {
            return false;
        }

        $timezone = $this->getTimezone();
        $resetDate = Carbon::parse($this->reset_counter_date, $timezone);

        if (!$resetDate->isToday()) {
            return false;
        }

        switch ($this->reset_counter_frequency_id) {
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

        $this->reset_counter_date = $resetDate->format('Y-m-d');
        $this->invoice_number_counter = 1;
        $this->quote_number_counter = 1;
        $this->credit_number_counter = $this->credit_number_counter > 0 ? 1 : 0;

        $this->save();
    }
}
