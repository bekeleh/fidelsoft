<?php

namespace App\Ninja\Transformers;

use App\Models\Bill;

/**
 * @SWG\Definition(definition="Bill", required={"invoice_number"}, @SWG\Xml(name="Bill"))
 */
class BillTransformer extends EntityTransformer
{
    /**
     * @SWG\Property(property="id", type="integer", example=1, readOnly=true)
     * @SWG\Property(property="amount", type="number", format="float", example=10, readOnly=true)
     * @SWG\Property(property="balance", type="number", format="float", example=10, readOnly=true)
     * @SWG\Property(property="vendor_id", type="integer", example=1)
     * @SWG\Property(property="invoice_number", type="string", example="0001")
     * @SWG\Property(property="private_notes", type="string", example="Notes...")
     * @SWG\Property(property="public_notes", type="string", example="Notes...")
     */
    protected $defaultIncludes = [
        'invoice_items',
    ];

    protected $availableIncludes = [
        'invitations',
        'payments',
        'vendor',
        'documents',
    ];

    public function __construct($account = null, $serializer = null, $vendor = null)
    {
        parent::__construct($account, $serializer);

        $this->vendor = $vendor;
    }

    public function includeBillItems(Bill $invoice)
    {
        $transformer = new BillItemTransformer($this->account, $this->serializer);

        return $this->includeCollection($invoice->invoice_items, $transformer, ENTITY_BILL_ITEM);
    }

    public function includeInvitations(Bill $invoice)
    {
        $transformer = new InvitationTransformer($this->account, $this->serializer);

        return $this->includeCollection($invoice->invitations, $transformer, ENTITY_INVITATION);
    }

    public function includePayments(Bill $invoice)
    {
        $transformer = new PaymentTransformer($this->account, $this->serializer, $invoice);

        return $this->includeCollection($invoice->payments, $transformer, ENTITY_PAYMENT);
    }

    public function includeClient(Bill $invoice)
    {
        $transformer = new ClientTransformer($this->account, $this->serializer);

        return $this->includeItem($invoice->vendor, $transformer, ENTITY_CLIENT);
    }

    public function includeExpenses(Bill $invoice)
    {
        $transformer = new ExpenseTransformer($this->account, $this->serializer);

        return $this->includeCollection($invoice->expenses, $transformer, ENTITY_EXPENSE);
    }

    public function includeDocuments(Bill $invoice)
    {
        $transformer = new DocumentTransformer($this->account, $this->serializer);

        $invoice->documents->each(function ($document) use ($invoice) {
            $document->setRelation('invoice', $invoice);
        });

        return $this->includeCollection($invoice->documents, $transformer, ENTITY_DOCUMENT);
    }

    public function transform(Bill $invoice)
    {
        return array_merge($this->getDefaults($invoice), [
            'id' => (int)$invoice->public_id,
            'vendor_id' => (int)($this->vendor ? $this->vendor->public_id : $invoice->vendor->public_id),
            'invoice_status_id' => (int)($invoice->invoice_status_id ?: 1),
            'updated_at' => $this->getTimestamp($invoice->updated_at),
            'archived_at' => $this->getTimestamp($invoice->deleted_at),
            'invoice_number' => $invoice->is_recurring ? '' : $invoice->invoice_number,
            'discount' => (float)$invoice->discount,
            'po_number' => $invoice->po_number,
            'invoice_date' => $invoice->invoice_date ?: '',
            'due_date' => $invoice->due_date ?: '',
            'terms' => $invoice->terms,
            'public_notes' => $invoice->public_notes ?: '',
            'private_notes' => $invoice->private_notes ?: '',
            'is_deleted' => (bool)$invoice->is_deleted,
            'invoice_type_id' => (int)$invoice->invoice_type_id,
            'is_recurring' => (bool)$invoice->is_recurring,
            'frequency_id' => (int)$invoice->frequency_id,
            'start_date' => $invoice->start_date ?: '',
            'end_date' => $invoice->end_date ?: '',
            'last_sent_date' => $invoice->last_sent_date ?: '',
            'recurring_invoice_id' => (int)($invoice->recurring_invoice_id ?: 0),
            'tax_name1' => $invoice->tax_name1 ? $invoice->tax_name1 : '',
            'tax_rate1' => (float)$invoice->tax_rate1,
            'tax_name2' => $invoice->tax_name2 ? $invoice->tax_name2 : '',
            'tax_rate2' => (float)$invoice->tax_rate2,
            'amount' => (float)$invoice->amount,
            'balance' => (float)$invoice->balance,
            'is_amount_discount' => (bool)($invoice->is_amount_discount ?: false),
            'invoice_footer' => $invoice->invoice_footer ?: '',
            'partial' => (float)($invoice->partial ?: 0.0),
            'partial_due_date' => $invoice->partial_due_date ?: '',
            'has_tasks' => (bool)$invoice->has_tasks,
            'auto_bill' => (bool)$invoice->auto_bill,
            'custom_value1' => (float)$invoice->custom_value1,
            'custom_value2' => (float)$invoice->custom_value2,
            'custom_taxes1' => (bool)$invoice->custom_taxes1,
            'custom_taxes2' => (bool)$invoice->custom_taxes2,
            'has_expenses' => (bool)$invoice->has_expenses,
            'quote_invoice_id' => (int)($invoice->quote_invoice_id ?: 0),
            'custom_text_value1' => $invoice->custom_text_value1 ?: '',
            'custom_text_value2' => $invoice->custom_text_value2 ?: '',
            'is_quote' => (bool)$invoice->isType(INVOICE_TYPE_QUOTE), // Temp to support mobile app
            'is_public' => (bool)$invoice->is_public,
            'filename' => $invoice->getFileName(),
        ]);
    }
}
