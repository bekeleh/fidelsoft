<?php

namespace App\Jobs;

use App\Libraries\Utils;
use CleverIt\UBL\Invoice\Address;
use CleverIt\UBL\Invoice\Contact;
use CleverIt\UBL\Invoice\Country;
use CleverIt\UBL\Invoice\Generator;
use CleverIt\UBL\Invoice\Invoice;
use CleverIt\UBL\Invoice\InvoiceLine;
use CleverIt\UBL\Invoice\Item;
use CleverIt\UBL\Invoice\LegalMonetaryTotal;
use CleverIt\UBL\Invoice\Party;
use CleverIt\UBL\Invoice\TaxCategory;
use CleverIt\UBL\Invoice\TaxScheme;
use CleverIt\UBL\Invoice\TaxSubTotal;
use CleverIt\UBL\Invoice\TaxTotal;
use Exception;

class ConvertBillToUbl extends Job
{
    const BILL_TYPE_STANDARD = 380;
    const BILL_TYPE_CREDIT = 381;

    public $bill;

    public function __construct($bill)
    {
        $this->bill = $bill;
    }

    public function handle()
    {
        $bill = $this->bill;
        $account = $bill->account;
        $vendor = $bill->vendor;
        $ublInvoice = new Invoice();

        // bill
        $ublInvoice->setId($bill->bill_number);
        $ublInvoice->setIssueDate(date_create($bill->bill_date));
        $ublInvoice->setInvoiceTypeCode($bill->amount < 0 ? self::BILL_TYPE_CREDIT : self::BILL_TYPE_STANDARD);

        $supplierParty = $this->createParty($account, $bill->user);
        $ublInvoice->setAccountingSupplierParty($supplierParty);

        $customerParty = $this->createParty($vendor, $vendor->contacts[0]);
        $ublInvoice->setAccountingCustomerParty($customerParty);

        // line items
        $billLines = [];
        $taxable = $bill->getTaxable();

        foreach ($bill->bill_items as $index => $item) {
            $itemTaxable = $bill->getItemTaxable($item, $taxable);
            $item->setRelation('bill', $bill);
            $billLines[] = $this->createInvoiceLine($index, $item, $itemTaxable);
        }

        $ublInvoice->setBillLines($billLines);

        $taxtotal = new TaxTotal();
        $taxAmount1 = $taxAmount2 = 0;

        $taxAmount1 = $this->createTaxRate($taxtotal, $taxable, $bill->tax_rate1, $bill->tax_name1);
        if ($bill->tax_name2 || floatval($bill->tax_rate2)) {
            $taxAmount2 = $this->createTaxRate($taxtotal, $taxable, $bill->tax_rate2, $bill->tax_name2);
        }

        $taxtotal->setTaxAmount($taxAmount1 + $taxAmount2);
        $ublInvoice->setTaxTotal($taxtotal);

        $ublInvoice->setLegalMonetaryTotal((new LegalMonetaryTotal())
            //->setLineExtensionAmount()
            ->setTaxExclusiveAmount($taxable)
            ->setPayableAmount($bill->balance));

        try {
            return Generator::bill($ublInvoice, $bill->vendor->getCurrencyCode());
        } catch (Exception $exception) {
            Utils::logError($exception);

            return false;
        }
    }

    private function createParty($company, $user)
    {
        $party = new Party();
        $party->setName($company->name);
        $address = (new Address())
            ->setCityName($company->city)
            ->setStreetName($company->address1)
            ->setBuildingNumber($company->address2)
            ->setPostalZone($company->postal_code);

        if ($company->country_id) {
            $country = new Country();
            $country->setIdentificationCode($company->country->iso_3166_2);
            $address->setCountry($country);
        }

        $party->setPostalAddress($address);
        $party->setPhysicalLocation($address);

        $contact = new Contact();
        $contact->setElectronicMail($user->email);
        $party->setContact($contact);

        return $party;
    }

    private function createInvoiceLine($index, $item, $taxable)
    {
        $billLine = (new InvoiceLine())
            ->setId($index + 1)
            ->setInvoicedQuantity($item->qty)
            ->setLineExtensionAmount($item->costWithDiscount())
            ->setItem((new Item())
                ->setName($item->name)
                ->setDescription($item->description));
        //->setSellersItemIdentification("1ABCD"));

        $taxtotal = new TaxTotal();
        $itemTaxAmount1 = $itemTaxAmount2 = 0;

        $itemTaxAmount1 = $this->createTaxRate($taxtotal, $taxable, $item->tax_rate1, $item->tax_name1);
        if ($item->tax_name2 || floatval($item->tax_rate2)) {
            $itemTaxAmount2 = $this->createTaxRate($taxtotal, $taxable, $item->tax_rate2, $item->tax_name2);
        }

        $taxtotal->setTaxAmount($itemTaxAmount1 + $itemTaxAmount2);
        $billLine->setTaxTotal($taxtotal);

        return $billLine;
    }

    private function createTaxRate(&$taxtotal, $taxable, $taxRate, $taxName)
    {
        $bill = $this->bill;
        $taxAmount = $bill->taxAmount($taxable, $taxRate);
        $taxScheme = ((new TaxScheme()))->setId($taxName);

        $taxtotal->addTaxSubTotal((new TaxSubTotal())
            ->setTaxAmount($taxAmount)
            ->setTaxableAmount($taxable)
            ->setTaxCategory((new TaxCategory())
                ->setId($taxName)
                ->setName($taxName)
                ->setTaxScheme($taxScheme)
                ->setPercent($taxRate)));

        return $taxAmount;
    }
}
