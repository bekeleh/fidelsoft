<?php

namespace App\Ninja\Transformers;

use App\Models\Common\Account;
use App\Models\Vendor;
use App\Models\Bill;
use App\Models\BillPayment;

/**
 * @SWG\Definition(definition="BillPayment", required={"bill_id"}, @SWG\Xml(name="BillPayment"))
 */
class BillPaymentTransformer extends EntityTransformer
{
    /**
     * @SWG\Property(property="id", type="integer", example=1, readOnly=true)
     * @SWG\Property(property="amount", type="number", format="float", example=10, readOnly=true)
     * @SWG\Property(property="transaction_reference", type="string", example="Transaction Reference")
     * @SWG\Property(property="payment_date", type="string", format="date", example="2018-01-01")
     * @SWG\Property(property="updated_at", type="integer", example=1451160233, readOnly=true)
     * @SWG\Property(property="archived_at", type="integer", example=1451160233, readOnly=true)
     * @SWG\Property(property="is_deleted", type="boolean", example=false, readOnly=true)
     * @SWG\Property(property="payment_type_id", type="integer", example=1)
     * @SWG\Property(property="bill_id", type="integer", example=1)
     * @SWG\Property(property="private_notes", type="string", example="Notes...")
     */
    protected $defaultIncludes = [];

    protected $availableIncludes = [
        'vendor',
        'bill',
    ];

    public function __construct($account = null, $serializer = null, $bill = null)
    {
        parent::__construct($account, $serializer);

        $this->bill = $bill;
    }

    public function includeBill(BillPayment $payment)
    {
        $transformer = new BillTransformer($this->account, $this->serializer);

        return $this->includeItem($payment->bill, $transformer, 'bill');
    }

    public function includeVendor(BillPayment $payment)
    {
        $transformer = new VendorTransformer($this->account, $this->serializer);

        return $this->includeItem($payment->vendor, $transformer, 'vendor');
    }

    public function transform(BillPayment $payment)
    {
        return array_merge($this->getDefaults($payment), [
            'id' => (int)$payment->public_id,
            'amount' => (float)$payment->amount,
            'transaction_reference' => $payment->transaction_reference ?: '',
            'payment_date' => $payment->payment_date ?: '',
            'updated_at' => $this->getTimestamp($payment->updated_at),
            'archived_at' => $this->getTimestamp($payment->deleted_at),
            'is_deleted' => (bool)$payment->is_deleted,
            'payment_type_id' => (int)($payment->payment_type_id ?: 0),
            'bill_id' => (int)($this->bill ? $this->bill->public_id : $payment->bill->public_id),
            'bill_number' => $this->bill ? $this->bill->bill_number : $payment->bill->bill_number,
            'private_notes' => $payment->private_notes ?: '',
            'exchange_rate' => (float)$payment->exchange_rate,
            'exchange_currency_id' => (int)$payment->exchange_currency_id,
            'refunded' => (float)$payment->refunded,
            'payment_status_id' => (int)($payment->payment_status_id ?: PAYMENT_STATUS_COMPLETED),
        ]);
    }
}
