<?php

namespace App\Models;

use App\Events\PurchaseInvitationWasEmailed;
use App\Events\PurchaseInvoiceWasCreated;
use App\Events\PurchaseInvoiceWasUpdated;
use App\Events\PurchaseQuoteInvitationWasEmailed;
use App\Events\PurchaseQuoteWasCreated;
use App\Events\PurchaseQuoteWasUpdated;
use App\Libraries\CurlUtils;
use App\Libraries\Utils;
use App\Models\Traits\ChargesFees;
use App\Models\Traits\HasRecurrence;
use App\Models\Traits\OwnedByVendorTrait;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Laracasts\Presenter\PresentableTrait;
use DateTime;

/**
 * Model Class PurchaseInvoice.
 */
class PurchaseInvoice extends EntityModel implements BalanceAffecting
{
    use PresentableTrait;
    use OwnedByVendorTrait;
    use ChargesFees;
    use HasRecurrence;

    use SoftDeletes {
        SoftDeletes::trashed as parentTrashed;
    }

    protected $presenter = 'App\Ninja\Presenters\PurchaseInvoicePresenter';

    protected $table = 'purchase_invoices';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['deleted_at'];


    protected $fillable = [
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'private_notes',
        'issue_date',
        'last_sent_date',
        'last_receive_date',
        'delivery_date',
        'due_date',
        'invoice_design_id',
        'branch_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'has_tasks' => 'boolean',
        'vendor_enable_auto_bill' => 'boolean',
        'has_expenses' => 'boolean',
    ];

    public static $patternFields = [
        'counter',
        'vendorCounter',
        'vendorIdNumber',
        'vendorCustom1',
        'vendorCustom2',
        'userId',
        'year',
        'date:',
    ];

    public static $requestFields = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'po_number',
        'discount',
        'partial',
    ];

    public static $statusClasses = [
        INVOICE_STATUS_SENT => 'info',
        INVOICE_STATUS_VIEWED => 'warning',
        INVOICE_STATUS_APPROVED => 'success',
        INVOICE_STATUS_PARTIAL => 'primary',
        INVOICE_STATUS_PAID => 'success',
    ];

    public static function getImportColumns()
    {
        return [
            'name',
            'email',
            'invoice_number',
            'po_number',
            'invoice_date',
            'due_date',
            'paid',
            'terms',
            'public_notes',
            'private_notes',
            'item_product',
            'item_notes',
            'item_quantity',
            'cost',
            'item_tax1',
            'item_tax2',
        ];
    }

    public static function getImportMap()
    {
        return [
            'number^po' => 'invoice_number',
            'vendor|organization' => 'name',
            'email' => 'email',
            'paid^date' => 'paid',
            'invoice date|create date' => 'invoice_date',
            'po number' => 'po_number',
            'due date' => 'due_date',
            'terms' => 'terms',
            'public notes' => 'public_notes',
            'private notes' => 'private_notes',
            'description' => 'item_notes',
            'quantity|qty' => 'item_quantity',
            'amount|cost' => 'cost',
            'product' => 'item_product',
            'tax' => 'item_tax1',
        ];
    }

    public function getEntityType()
    {
        return $this->isType(PURCHASE_INVOICE_TYPE_QUOTE) ? ENTITY_PURCHASE_QUOTE : ENTITY_PURCHASE_INVOICE;
    }

    public function subEntityType()
    {
        if ($this->is_recurring) {
            return ENTITY_PURCHASE_RECURRING_INVOICE;
        } else {
            return $this->getEntityType();
        }
    }

    public function getRoute()
    {
        if ($this->is_recurring) {
            $entityType = 'purchase_recurring_invoice';
        } else {
            $entityType = $this->getEntityType();
        }

        return "/{$entityType}s/{$this->public_id}/edit";
    }

    public function getDisplayName()
    {
        return $this->is_recurring ? trans('texts.recurring') : $this->invoice_number;
    }


    public function affectsBalance()
    {
        return $this->isType(PURCHASE_INVOICE_TYPE_STANDARD) && !$this->is_recurring && $this->is_public;
    }

    public function getAdjustment()
    {
        if (!$this->affectsBalance()) {
            return 0;
        }

        return $this->getRawAdjustment();
    }

//  balance adjustment
    private function getRawAdjustment()
    {
        // if we've just made the invoice public then apply the full amount
        if ($this->is_public && !$this->getOriginal('is_public')) {
            return $this->amount;
        }

        return floatval($this->amount) - floatval($this->getOriginal('amount'));
    }

    public function isChanged()
    {
        if (Utils::isNinja()) {
            if ($this->getRawAdjustment() != 0) {
                return true;
            }

            foreach ([
                         'invoice_number',
                         'po_number',
                         'invoice_date',
                         'due_date',
                         'terms',
                         'public_notes',
                         'invoice_footer',
                         'partial',
                         'partial_due_date',
                     ] as $field) {
                if ($this->$field != $this->getOriginal($field)) {
                    return true;
                }
            }

            return false;
        } else {
            $dirty = $this->getDirty();

            unset($dirty['invoice_status_id']);
            unset($dirty['vendor_enable_auto_bill']);
            unset($dirty['quote_invoice_id']);

            return count($dirty) > 0;
        }
    }

    public function getAmountPaid($calculate = false)
    {
        if ($this->isType(PURCHASE_INVOICE_TYPE_QUOTE) || $this->is_recurring) {
            return 0;
        }

        if ($calculate) {
            $amount = 0;
            foreach ($this->payments as $payment) {
                if ($payment->payment_status_id == PAYMENT_STATUS_VOIDED || $payment->payment_status_id == PAYMENT_STATUS_FAILED) {
                    continue;
                }
                $amount += $payment->getCompletedAmount();
            }

            return $amount;
        } else {
            return $this->amount - $this->balance;
        }
    }

    public function trashed()
    {
        if ($this->vendor && $this->vendor->trashed()) {
            return true;
        }

        return self::parentTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
    }

//  to cheat the vendor model
    public function client()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendor_id')->withTrashed();
    }

    public function invoice_items()
    {
        return $this->hasMany('App\Models\PurchaseInvoiceItem', 'purchase_invoice_id')->orderBy('id');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document')->orderBy('id');
    }


    public function allDocuments()
    {
        $documents = $this->documents;
        $documents = $documents->merge($this->account->defaultDocuments);

        foreach ($this->expenses as $expense) {
            if ($expense->invoice_documents) {
                $documents = $documents->merge($expense->documents);
            }
        }

        return $documents;
    }


    public function invoice_status()
    {
        return $this->belongsTo('App\Models\InvoiceStatus');
    }


    public function invoice_design()
    {
        return $this->belongsTo('App\Models\PurchaseInvoiceDesign');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PurchasePayment');
    }

    public function recurring_invoice()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice');
    }

    public function quote()
    {
        return $this->belongsTo('App\Models\PurchaseInvoice')->withTrashed();
    }

    public function recurring_invoices()
    {
        return $this->hasMany('App\Models\PurchaseInvoice', 'recurring_invoice_id');
    }

    public function frequency()
    {
        return $this->belongsTo('App\Models\Frequency');
    }

    public function invitations()
    {
        return $this->hasMany('App\Models\PurchaseInvitation')->orderBy('purchase_invitations.contact_id');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch')->withTrashed();
    }

    public function activities()
    {
        return $this->hasMany('App\Models\Activity')->withTrashed();
    }

    public function scopePurchaseInvoices($query)
    {
        return $query->where('invoice_type_id', PURCHASE_INVOICE_TYPE_STANDARD)
            ->where('is_recurring', false);
    }

    public function scopeRecurring($query)
    {
        return $query->where('invoice_type_id', PURCHASE_INVOICE_TYPE_STANDARD)
            ->where('is_recurring', true);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('invoice_date', [$startDate, $endDate]);
        })->orWhere(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        });
    }

    public function scopeQuotes($query)
    {
        return $query->where('invoice_type_id', PURCHASE_INVOICE_TYPE_QUOTE)
            ->where('is_recurring', false);
    }

    public function scopeUnapprovedQuotes($query, $includePurchaseInvoiceId = false)
    {
        return $query->quotes()
            ->where(function ($query) use ($includePurchaseInvoiceId) {
                $query->whereId($includePurchaseInvoiceId)
                    ->orWhere(function ($query) {
                        $query->where('invoice_status_id', '<', INVOICE_STATUS_APPROVED)
                            ->whereNull('quote_invoice_id');
                    });
            });
    }

    public function scopeInvoiceType($query, $typeId)
    {
        return $query->where('invoice_type_id', $typeId);
    }

    public function scopeStatusIds($query, $statusIds)
    {
        if (!$statusIds || (is_array($statusIds) && !count($statusIds))) {
            return $query;
        }

        return $query->where(function ($query) use ($statusIds) {
            foreach ($statusIds as $statusId) {
                $query->orWhere('invoice_status_id', $statusId);
            }
            if (in_array(INVOICE_STATUS_UNPAID, $statusIds)) {
                $query->orWhere(function ($query) {
                    $query->where('balance', '>', 0)
                        ->where('is_public', true);
                });
            }
            if (in_array(INVOICE_STATUS_OVERDUE, $statusIds)) {
                $query->orWhere(function ($query) {
                    $query->where('balance', '>', 0)
                        ->where('due_date', '<', date('Y-m-d'))
                        ->where('is_public', true);
                });
            }
        });
    }

    public function isType($typeId)
    {
        return $this->invoice_type_id == $typeId;
    }

    public function isQuote()
    {
        return $this->isType(PURCHASE_INVOICE_TYPE_QUOTE);
    }

    public function getCustomMessageType()
    {
        if ($this->isQuote()) {
            return $this->quote_invoice_id ? CUSTOM_MESSAGE_APPROVED_QUOTE : CUSTOM_MESSAGE_UNAPPROVED_QUOTE;
        } else {
            return $this->balance > 0 ? CUSTOM_MESSAGE_UNPAID_INVOICE : CUSTOM_MESSAGE_PAID_INVOICE;
        }
    }

    public function isStandard()
    {
        return $this->isType(PURCHASE_INVOICE_TYPE_STANDARD) && !$this->is_recurring;
    }

    public function markSentIfUnsent()
    {
        if (!$this->isSent()) {
            $this->markSent();
        }
    }

    public function markSent()
    {
        if ($this->is_deleted) {
            return false;
        }

        if (!$this->isSent()) {
            $this->invoice_status_id = INVOICE_STATUS_SENT;
        }

        $this->is_public = true;
        $this->save();

        $this->markInvitationsSent();
    }

    public function markInvitationsSent($notify = false, $reminder = false)
    {
        if ($this->is_deleted) {
            return false;
        }

        if (!$this->relationLoaded('purchase_invitations')) {
            $this->load('purchase_invitations');
        }

        foreach ($this->purchase_invitations as $invitation) {
            $this->markInvitationSent($invitation, false, $notify, $reminder);
        }
    }

    public function areInvitationsSent()
    {
        if (!$this->relationLoaded('purchase_invitations')) {
            $this->load('purchase_invitations');
        }

        foreach ($this->purchase_invitations as $invitation) {
            if (!$invitation->isSent()) {
                return false;
            }
        }

        return true;
    }

    public function markInvitationSent($invitation, $messageId = false, $notify = true, $notes = false)
    {
        if ($this->is_deleted) {
            return false;
        }

        if (!$this->isSent()) {
            $this->is_public = true;
            $this->invoice_status_id = INVOICE_STATUS_SENT;
            $this->save();
        }

        $invitation->markSent($messageId);

        // if the user marks it as sent rather than actually sending it
        // then we won't track it in the activity log
        if (!$notify) {
            return false;
        }

        if ($this->isType(PURCHASE_INVOICE_TYPE_QUOTE)) {
            event(new PurchaseQuoteInvitationWasEmailed($invitation, $notes));
        } else {
            event(new PurchaseInvitationWasEmailed($invitation, $notes));
        }
    }

    public function markViewed()
    {
        if (!$this->isViewed()) {
            $this->invoice_status_id = INVOICE_STATUS_VIEWED;
            $this->save();
        }
    }

    public function updatePaidStatus($paid = false, $save = true)
    {
        $statusId = false;
        if ($paid && $this->balance == 0) {
            $statusId = INVOICE_STATUS_PAID;
        } elseif ($paid && $this->balance > 0 && $this->balance < $this->amount) {
            $statusId = INVOICE_STATUS_PARTIAL;
        } elseif ($this->isPartial() && $this->balance > 0) {
            $statusId = ($this->balance == $this->amount ? INVOICE_STATUS_SENT : INVOICE_STATUS_PARTIAL);
        }

        if ($statusId && $statusId != $this->invoice_status_id) {
            $this->invoice_status_id = $statusId;
            if ($save) {
                $this->save();
            }
        }
    }

    public function markApproved()
    {
        if ($this->isType(PURCHASE_INVOICE_TYPE_QUOTE)) {
            $this->invoice_status_id = INVOICE_STATUS_APPROVED;
            $this->save();
        }
    }

    public function updateBalances($balanceAdjustment, $partial = 0)
    {
        if ($this->is_deleted) {
            return false;
        }

        $balanceAdjustment = floatval($balanceAdjustment);
        $partial = floatval($partial);

        if (!$balanceAdjustment && $this->partial == $partial) {
            return false;
        }

        $this->balance = $this->balance + $balanceAdjustment;

        if ($this->partial > 0) {
            $this->partial = $partial;

            // clear the partial due date and set the due date
            // using payment terms if it's blank
            if (!$this->partial && $this->partial_due_date) {
                $this->partial_due_date = null;
                if (!$this->due_date) {
                    $this->due_date = $this->account->defaultDueDate($this->vendor);
                }
            }
        }

        $this->save();

        // mark fees as paid
        if ($balanceAdjustment != 0 && $this->account->gateway_fee_enabled) {
            if ($purchaseInvoiceItem = $this->getGatewayFeeItem()) {
                $purchaseInvoiceItem->markFeePaid();
            }
        }
    }

    public function activeUser()
    {
        if (!$this->user->trashed()) {
            return $this->user;
        }

        return $this->account->users->first();
    }

    public function getName()
    {
        return $this->is_recurring ? trans('texts.recurring') : $this->invoice_number;
    }

    public function getFileName($extension = 'pdf')
    {
        $entityType = $this->getEntityType();

        return trans("texts.$entityType") . '_' . $this->invoice_number . '.' . $extension;
    }

    public function getPDFPath()
    {
        return storage_path() . '/pdfcache/cache-' . $this->id . '.pdf';
    }

    public function canBePaid()
    {
        return !$this->isPaid() && !$this->is_deleted && $this->isStandard();
    }

    public static function calcStatusLabel($status, $class, $entityType, $quotePurchaseInvoiceId)
    {
        if ($quotePurchaseInvoiceId) {
            $label = 'converted';
        } elseif ($class == 'danger') {
            $label = $entityType == ENTITY_PURCHASE_INVOICE ? 'past_due' : 'expired';
        } else {
            $label = 'status_' . strtolower($status);
        }

        return trans("texts.{$label}");
    }

    public static function calcStatusClass($statusId, $balance, $dueDate, $isRecurring)
    {
        if ($statusId >= INVOICE_STATUS_SENT && !$isRecurring && static::calcIsOverdue($balance, $dueDate)) {
            return 'danger';
        }

        if (!empty(static::$statusClasses[$statusId])) {
            return static::$statusClasses[$statusId];
        }

        return 'default';
    }

    public static function calcIsOverdue($balance, $dueDate)
    {
        if (!Utils::parseFloat($balance) > 0) {
            return false;
        }

        if (!$dueDate || $dueDate == '0000-00-00') {
            return false;
        }

        // it isn't considered overdue until the end of the day
        return time() > (strtotime($dueDate) + (60 * 60 * 24));
    }

    public function statusClass()
    {
        $dueDate = $this->getOriginal('partial_due_date') ?: $this->getOriginal('due_date');
        return static::calcStatusClass($this->invoice_status_id, $this->balance, $dueDate, $this->is_recurring);
    }

    public function statusLabel()
    {
        return static::calcStatusLabel($this->invoice_status->name, $this->statusClass(), $this->getEntityType(), $this->quote_invoice_id);
    }

    public static function calcLink($purchaseInvoice)
    {
        if (!empty($purchaseInvoice->invoice_type_id)) {
            $linkPrefix = ($purchaseInvoice->invoice_type_id == PURCHASE_INVOICE_TYPE_QUOTE) ? 'purchase_quotes/' : 'purchase_invoices/';
        } else {
            $linkPrefix = 'invoices/';
        }
        return link_to($linkPrefix . $purchaseInvoice->public_id, $purchaseInvoice->invoice_number);
    }

    public function getLink()
    {
        return self::calcLink($this);
    }

    public function getInvitationLink($type = 'view', $forceOnsite = false, $forcePlain = false)
    {
        if (!$this->relationLoaded('purchase_invitations')) {
            $this->load('purchase_invitations');
        }

        return $this->purchase_invitations[0]->getLink($type, $forceOnsite, $forcePlain);
    }

    public function isSent()
    {
        return $this->invoice_status_id >= INVOICE_STATUS_SENT && $this->getOriginal('is_public');
    }

    public function isViewed()
    {
        return $this->invoice_status_id >= INVOICE_STATUS_VIEWED;
    }

    public function isApproved()
    {
        return $this->invoice_status_id >= INVOICE_STATUS_APPROVED || $this->quote_invoice_id;
    }

    public function isPartial()
    {
        return $this->invoice_status_id >= INVOICE_STATUS_PARTIAL;
    }

    public function isPaid()
    {
        return $this->invoice_status_id >= INVOICE_STATUS_PAID;
    }

    public function isOverdue()
    {
        return static::calcIsOverdue($this->balance, $this->partial_due_date ?: $this->due_date);
    }

    public function getRequestedAmount()
    {
        $fee = 0;
        if ($this->account->gateway_fee_enabled) {
            $fee = $this->getGatewayFee();
        }

        if ($this->partial > 0) {
            return $this->partial + $fee;
        } else {
            return $this->balance;
        }
    }

    public function getCurrencyCode()
    {
        if ($this->vendor->currency) {
            return $this->vendor->currency->code;
        } elseif ($this->account->currency) {
            return $this->account->currency->code;
        } else {
            return 'USD';
        }
    }

    public function hidePrivateFields()
    {
        $this->setVisible([
            'invoice_number',
            'discount',
            'is_amount_discount',
            'po_number',
            'invoice_date',
            'due_date',
            'terms',
            'invoice_footer',
            'public_notes',
            'amount',
            'balance',
            'invoice_items',
            'documents',
            'expenses',
            'vendor',
            'invitations',
            'tax_name1',
            'tax_rate1',
            'tax_name2',
            'tax_rate2',
            'account',
            'invoice_design',
            'invoice_design_id',
            'invoice_fonts',
            'features',
            'invoice_type_id',
            'custom_value1',
            'custom_value2',
            'custom_taxes1',
            'custom_taxes2',
            'partial',
            'partial_due_date',
            'has_tasks',
            'custom_text_value1',
            'custom_text_value2',
            'has_expenses',
        ]);

        $this->vendor->setVisible([
            'name',
            'balance',
            'id_number',
            'vat_number',
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'work_phone',
            'payment_terms',
            'website',
            'contacts',
            'country',
            'currency_id',
            'country_id',
            'custom_value1',
            'custom_value2',
        ]);

        $this->account->setVisible([
            'name',
            'website',
            'id_number',
            'vat_number',
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'work_phone',
            'work_email',
            'country',
            'country_id',
            'currency_id',
            'custom_fields',
            'custom_value1',
            'custom_value2',
            'primary_color',
            'secondary_color',
            'hide_quantity',
            'hide_paid_to_date',
            'all_pages_header',
            'all_pages_footer',
            'pdf_email_attachment',
            'show_item_taxes',
            'invoice_embed_documents',
            'page_size',
            'include_item_taxes_inline',
            'invoice_fields',
            'show_currency_code',
            'inclusive_taxes',
            'date_format',
            'datetime_format',
            'timezone',
            'signature_on_pdf',
        ]);

        foreach ($this->invitations as $invitation) {
            $invitation->setVisible([
                'signature_base64',
                'signature_date',
            ]);
        }

        foreach ($this->invoice_items as $purchaseInvoiceItem) {
            $purchaseInvoiceItem->setVisible([
                'name',
                'notes',
                'custom_value1',
                'custom_value2',
                'cost',
                'qty',
                'tax_name1',
                'tax_rate1',
                'tax_name2',
                'tax_rate2',
                'invoice_item_type_id',
                'discount',
            ]);
        }

        foreach ($this->vendor->contacts as $contact) {
            $contact->setVisible([
                'first_name',
                'last_name',
                'email',
                'phone',
                'custom_value1',
                'custom_value2',
            ]);
        }

        foreach ($this->documents as $document) {
            $document->setVisible([
                'public_id',
                'name',
            ]);
        }

        foreach ($this->expenses as $expense) {
            $expense->setVisible([
                'documents',
            ]);

            foreach ($expense->documents as $document) {
                $document->setVisible([
                    'public_id',
                    'name',
                ]);
            }
        }

        return $this;
    }

    public function getDueDate($purchaseInvoice_date = null)
    {
        if (!$this->is_recurring) {
            return $this->due_date ? $this->due_date : null;
        } else {
            $now = time();
            if ($purchaseInvoice_date) {
                // If $purchaseInvoice_date is specified, all calculations are based on that date
                if (is_numeric($purchaseInvoice_date)) {
                    $now = $purchaseInvoice_date;
                } elseif (is_string($purchaseInvoice_date)) {
                    $now = strtotime($purchaseInvoice_date);
                } elseif ($purchaseInvoice_date instanceof DateTime) {
                    $now = $purchaseInvoice_date->getTimestamp();
                }
            }

            if ($this->due_date && $this->due_date != '0000-00-00') {
                // This is a recurring invoice; we're using a custom format here.
                // The year is always 1998; January is 1st, 2nd, last day of the month.
                // February is 1st Sunday after, 1st Monday after, ..., through 4th Saturday after.
                $dueDateVal = strtotime($this->due_date);
                $monthVal = (int)date('n', $dueDateVal);
                $dayVal = (int)date('j', $dueDateVal);
                $dueDate = false;

                if ($monthVal == 1) {// January; day of month
                    $currentDay = (int)date('j', $now);
                    $lastDayOfMonth = (int)date('t', $now);

                    $dueYear = (int)date('Y', $now); // This year
                    $dueMonth = (int)date('n', $now); // This month
                    $dueDay = $dayVal; // The day specified for the invoice

                    if ($dueDay > $lastDayOfMonth) {
                        // No later than the end of the month
                        $dueDay = $lastDayOfMonth;
                    }

                    if ($currentDay >= $dueDay) {
                        // Wait until next month
                        // We don't need to handle the December->January wraparaound, since PHP handles month 13 as January of next year
                        $dueMonth++;

                        // Reset the due day
                        $dueDay = $dayVal;
                        $lastDayOfMonth = (int)date('t', mktime(0, 0, 0, $dueMonth, 1, $dueYear)); // The number of days in next month

                        // Check against the last day again
                        if ($dueDay > $lastDayOfMonth) {
                            // No later than the end of the month
                            $dueDay = $lastDayOfMonth;
                        }
                    }

                    $dueDate = mktime(0, 0, 0, $dueMonth, $dueDay, $dueYear);
                } elseif ($monthVal == 2) {// February; day of week
                    $ordinals = ['first', 'second', 'third', 'fourth'];
                    $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

                    $ordinalIndex = ceil($dayVal / 7) - 1; // 1-7 are "first"; 8-14 are "second", etc.
                    $dayOfWeekIndex = ($dayVal - 1) % 7; // 1,8,15,22 are Sunday, 2,9,16,23 are Monday, etc.
                    $dayStr = $ordinals[$ordinalIndex] . ' ' . $daysOfWeek[$dayOfWeekIndex]; // "first sunday", "first monday", etc.

                    $dueDate = strtotime($dayStr, $now);
                }

                if ($dueDate) {
                    return date('Y-m-d', $dueDate); // SQL format
                }
            } elseif ($this->vendor->payment_terms != 0) {
                // No custom due date set for this invoice; use the vendor's payment terms
                $days = $this->vendor->defaultDaysDue();

                return date('Y-m-d', strtotime('+' . $days . ' day', $now));
            } elseif ($this->account->payment_terms != 0) {
                $days = $this->account->defaultDaysDue();

                return date('Y-m-d', strtotime('+' . $days . ' day', $now));
            } elseif ($this->account->payment_terms != 0) {
                // No custom due date set for this invoice; use the vendor's payment terms
                $days = $this->account->payment_terms;
                if ($days == -1) {
                    $days = 0;
                }
                return date('Y-m-d', strtotime('+' . $days . ' day', $now));
            }
        }

        // Couldn't calculate one
        return null;
    }

    public function getPrettySchedule($min = 0, $max = 10)
    {
        if (!$schedule = $this->getSchedule($max)) {
            return null;
        }

        $dates = [];

        for ($i = $min; $i < min($max, count($schedule)); $i++) {
            $date = $schedule[$i];
            $dateStart = $date->getStart();
            $date = $this->account->formatDate($dateStart);
            $dueDate = $this->getDueDate($dateStart);

            if ($dueDate) {
                $date .= ' <small>(' . trans('texts.due') . ' ' . $this->account->formatDate($dueDate) . ')</small>';
            }

            $dates[] = $date;
        }

        return implode('<br/>', $dates);
    }

    public function getPDFString($invitation = false, $decode = true)
    {
        if (!env('PHANTOMJS_CLOUD_KEY') && !env('PHANTOMJS_BIN_PATH')) {
            return false;
        }

        if (Utils::isTravis()) {
            return false;
        }

        $invitation = $invitation ?: $this->purchase_invitations[0];
        $link = $invitation->getLink('view', true, true);
        $pdfString = false;
        $phantomjsSecret = env('PHANTOMJS_SECRET');
        $phantomjsLink = $link . "?phantomjs=true&phantomjs_secret={$phantomjsSecret}";

        try {
            if (env('PHANTOMJS_BIN_PATH')) {
                // we see occasional 408 errors
                for ($i = 1; $i <= 5; $i++) {
                    $pdfString = CurlUtils::phantom('GET', $phantomjsLink);
                    $pdfString = strip_tags($pdfString);
                    if (strpos($pdfString, 'data') === 0) {
                        break;
                    } else {
                        if (Utils::isNinjaDev() || Utils::isTravis()) {
                            Utils::logError('Failed to generate: ' . $i);
                        }
                        $pdfString = false;
                        sleep(2);
                    }
                }
            }

            if (!$pdfString && ($key = env('PHANTOMJS_CLOUD_KEY'))) {
                $url = "http://api.phantomjscloud.com/api/browser/v2/{$key}/?request=%7Burl:%22{$link}?phantomjs=true%26phantomjs_secret={$phantomjsSecret}%22,renderType:%22html%22%7D";
                $pdfString = CurlUtils::get($url);
                $pdfString = strip_tags($pdfString);
            }
        } catch (Exception $exception) {
            Utils::logError("PhantomJS - Failed to load {$phantomjsLink}: {$exception->getMessage()}");
            return false;
        }

        if (!$pdfString || strlen($pdfString) < 200) {
            Utils::logError("PhantomJS - Invalid response {$phantomjsLink}: {$pdfString}");
            return false;
        }

        if ($decode) {
            if ($pdf = Utils::decodePDF($pdfString)) {
                return $pdf;
            } else {
                Utils::logError("PhantomJS - Unable to decode {$phantomjsLink}");
                return false;
            }
        } else {
            return $pdfString;
        }
    }

    public function getItemTaxable($purchaseInvoiceItem, $purchaseInvoiceTotal)
    {
        $total = $purchaseInvoiceItem->qty * $purchaseInvoiceItem->cost;

        if ($this->discount != 0) {
            if ($this->is_amount_discount) {
                $total -= $purchaseInvoiceTotal ? ($total / ($purchaseInvoiceTotal + $this->discount) * $this->discount) : 0;
            } else {
                $total *= (100 - $this->discount) / 100;
            }
        }

        if ($purchaseInvoiceItem->discount != 0) {
            if ($this->is_amount_discount) {
                $total -= $purchaseInvoiceItem->discount;
            } else {
                $total -= $total * $purchaseInvoiceItem->discount / 100;
            }
        }

        return round($total, 2);
    }

    public function getTaxable()
    {
        $total = 0;

        foreach ($this->invoice_items as $purchaseInvoiceItem) {
            $lineTotal = $purchaseInvoiceItem->qty * $purchaseInvoiceItem->cost;

            if ($purchaseInvoiceItem->discount != 0) {
                if ($this->is_amount_discount) {
                    $lineTotal -= $purchaseInvoiceItem->discount;
                } else {
                    $lineTotal -= $lineTotal * $purchaseInvoiceItem->discount / 100;
                }
            }

            $total += $lineTotal;
        }

        if ($this->discount > 0) {
            if ($this->is_amount_discount) {
                $total -= $this->discount;
            } else {
                $total *= (100 - $this->discount) / 100;
                $total = round($total, 2);
            }
        }

        if ($this->custom_value1 && $this->custom_taxes1) {
            $total += $this->custom_value1;
        }

        if ($this->custom_value2 && $this->custom_taxes2) {
            $total += $this->custom_value2;
        }

        return $total;
    }

    // if $calculatePaid is true we'll loop through each payment to
    // determine the sum, otherwise we'll use the cached paid_to_date amount

    public function getTaxes($calculatePaid = false)
    {
        $taxes = [];
        $account = $this->account;
        $taxable = $this->getTaxable();
        $paidAmount = $this->getAmountPaid($calculatePaid);

        if ($this->tax_name1) {
            $purchaseInvoiceTaxAmount = $this->taxAmount($taxable, $this->tax_rate1);
            $purchaseInvoicePaidAmount = floatval($this->amount) && $purchaseInvoiceTaxAmount ? ($paidAmount / $this->amount * $purchaseInvoiceTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name1, $this->tax_rate1, $purchaseInvoiceTaxAmount, $purchaseInvoicePaidAmount);
        }
        if ($this->tax_name2) {
            $purchaseInvoiceTaxAmount = $this->taxAmount($taxable, $this->tax_rate2);
            $purchaseInvoicePaidAmount = floatval($this->amount) && $purchaseInvoiceTaxAmount ? ($paidAmount / $this->amount * $purchaseInvoiceTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name2, $this->tax_rate2, $purchaseInvoiceTaxAmount, $purchaseInvoicePaidAmount);
        }

        foreach ($this->invoice_items as $purchaseInvoiceItem) {
            $itemTaxable = $this->getItemTaxable($purchaseInvoiceItem, $taxable);

            if ($purchaseInvoiceItem->tax_name1) {
                $itemTaxAmount = $this->taxAmount($itemTaxable, $purchaseInvoiceItem->tax_rate1);
                $itemPaidAmount = floatval($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $purchaseInvoiceItem->tax_name1, $purchaseInvoiceItem->tax_rate1, $itemTaxAmount, $itemPaidAmount);
            }
            if ($purchaseInvoiceItem->tax_name2) {
                $itemTaxAmount = $this->taxAmount($itemTaxable, $purchaseInvoiceItem->tax_rate2);
                $itemPaidAmount = floatval($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $purchaseInvoiceItem->tax_name2, $purchaseInvoiceItem->tax_rate2, $itemTaxAmount, $itemPaidAmount);
            }
        }

        return $taxes;
    }

    public function getTaxTotal()
    {
        $total = 0;

        foreach ($this->getTaxes() as $tax) {
            $total += $tax['amount'];
        }

        return $total;
    }

    public function taxAmount($taxable, $rate)
    {
        $account = $this->account;

        if ($account->inclusive_taxes) {
            return round($taxable - ($taxable / (1 + ($rate / 100))), 2);
        } else {
            return round($taxable * ($rate / 100), 2);
        }
    }

    private function calculateTax(&$taxes, $name, $rate, $amount, $paid)
    {
        if (!$amount) {
            return false;
        }

        $amount = round($amount, 2);
        $paid = round($paid, 2);
        $key = $rate . ' ' . $name;

        if (empty($taxes[$key])) {
            $taxes[$key] = [
                'name' => $name,
                'rate' => $rate + 0,
                'amount' => 0,
                'paid' => 0,
            ];
        }

        $taxes[$key]['amount'] += $amount;
        $taxes[$key]['paid'] += $paid;
    }

    public function countDocuments($expenses = false)
    {
        $count = $this->documents->count();

        foreach ($this->expenses as $expense) {
            if ($expense->invoice_documents) {
                $count += $expense->documents->count();
            }
        }

        if ($expenses) {
            foreach ($expenses as $expense) {
                if ($expense->invoice_documents) {
                    $count += $expense->documents->count();
                }
            }
        }

        return $count;
    }

    public function hasDocuments()
    {
        if ($this->documents->count()) {
            return true;
        }

        if ($this->account->defaultDocuments->count()) {
            return true;
        }

        return $this->hasExpenseDocuments();
    }

    public function hasExpenseDocuments()
    {
        foreach ($this->expenses as $expense) {
            if ($expense->invoice_documents && $expense->documents->count()) {
                return true;
            }
        }

        return false;
    }

    public function getAutoBillEnabled()
    {
        if (!$this->is_recurring) {
            $recurPurchaseInvoice = $this->recurring_invoice;
        } else {
            $recurPurchaseInvoice = $this;
        }

        if (!$recurPurchaseInvoice) {
            return false;
        }

        return $recurPurchaseInvoice->auto_bill == AUTO_BILL_ALWAYS || ($recurPurchaseInvoice->auto_bill != AUTO_BILL_OFF && $recurPurchaseInvoice->vendor_enable_auto_bill);
    }

    public static function getStatuses($entityType = false)
    {
        $statuses = [];

        if ($entityType == ENTITY_RECURRING_INVOICE) {
            return $statuses;
        }

        foreach (Cache::get('invoiceStatus') as $status) {
            if ($entityType == ENTITY_PURCHASE_QUOTE) {
                if (in_array($status->id, [INVOICE_STATUS_PAID, INVOICE_STATUS_PARTIAL])) {
                    continue;
                }
            } elseif ($entityType == ENTITY_PURCHASE_INVOICE) {
                if (in_array($status->id, [INVOICE_STATUS_APPROVED])) {
                    continue;
                }
            }

            $statuses[$status->id] = trans('texts.status_' . strtolower($status->name));
        }

        if ($entityType == ENTITY_PURCHASE_INVOICE) {
            $statuses[INVOICE_STATUS_UNPAID] = trans('texts.unpaid');
            $statuses[INVOICE_STATUS_OVERDUE] = trans('texts.past_due');
        }

        return $statuses;
    }

    public function emailHistory()
    {
        return Activity::scope()
            ->with(['vendor_contact'])
            ->where('purchase_invoice_id', $this->id)
            ->whereIn('activity_type_id', [ACTIVITY_TYPE_EMAIL_INVOICE, ACTIVITY_TYPE_EMAIL_QUOTE])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getDateLabel()
    {
        return $this->getEntityType() === ENTITY_PURCHASE_INVOICE ? 'invoice_date' : 'quote_date';
    }

    public function getDueDateLabel()
    {
        return $this->isQuote() ? 'valid_until' : 'due_date';
    }

    public function onlyHasTasks()
    {
        foreach ($this->invoice_items as $item) {
            if ($item->invoice_item_type_id != INVOICE_ITEM_TYPE_TASK) {
                return false;
            }
        }

        return true;
    }

    public function hasTaxes()
    {
        if ($this->tax_name1 || $this->tax_rate1) {
            return true;
        }

        if ($this->tax_name2 || $this->tax_rate2) {
            return false;
        }

        return false;
    }

    public function isLocked()
    {
        if (!$lockSentPurchaseInvoice = config('ninja.lock_sent_invoices')) {
            return false;
        }

        if ($this->is_recurring) {
            return false;
        }

    }

    public function getPurchaseInvoiceLinkForQuote($vendorContactId)
    {
        if (!$this->quote_invoice_id) {
            return false;
        }

        $purchaseInvoice = static::scope($this->quote_invoice_id, $this->account_id)->with('invitations')->first();

        if (!$purchaseInvoice) {
            return false;
        }

        foreach ($purchaseInvoice->invitations as $invitation) {
            if ($invitation->contact_id == $vendorContactId) {
                return $invitation->getLink();
            }
        }

        return false;
    }
}

// invoice counter maybe not required for purchase invoice case
PurchaseInvoice::creating(function ($purchaseInvoice) {
    if (!$purchaseInvoice->is_recurring) {
        $account = $purchaseInvoice->account;
        if ($purchaseInvoice->amount >= 0) {
            $account->purchaseIncrementCounter($purchaseInvoice);
        } elseif ($account->credit_number_counter > 0) {
            $account->purchaseIncrementCounter(new PurchaseCredit());
        }
    }
});

PurchaseInvoice::created(function ($purchaseInvoice) {
    if ($purchaseInvoice->isType(PURCHASE_INVOICE_TYPE_QUOTE)) {
        event(new PurchaseQuoteWasCreated($purchaseInvoice));
    } else {
        event(new PurchaseInvoiceWasCreated($purchaseInvoice));
    }
});

PurchaseInvoice::updating(function ($purchaseInvoice) {
    if ($purchaseInvoice->isType(PURCHASE_INVOICE_TYPE_QUOTE)) {
        event(new PurchaseQuoteWasUpdated($purchaseInvoice));
    } else {
        event(new PurchaseInvoiceWasUpdated($purchaseInvoice));
    }
});
