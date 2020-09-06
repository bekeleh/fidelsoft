<?php

namespace App\Models;

use App\Events\Purchase\BillInvitationWasEmailedEvent;
use App\Events\Purchase\BillQuoteInvitationWasEmailedEvent;
use App\Events\Purchase\BillQuoteWasCreatedEvent;
use App\Events\Purchase\BillQuoteWasUpdatedEvent;
use App\Events\Purchase\BillWasCreatedEvent;
use App\Events\Purchase\BillWasUpdatedEvent;
use App\Libraries\CurlUtils;
use App\Libraries\Utils;
use App\Models\Traits\ChargesFees;
use App\Models\Traits\HasRecurrence;
use App\Models\Traits\OwnedByVendorTrait;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Bill.
 */
class Bill extends EntityModel implements BalanceAffecting
{
    use PresentableTrait;
    use OwnedByVendorTrait;
    use ChargesFees;
    use HasRecurrence;

    use SoftDeletes {
        SoftDeletes::trashed as parentTrashed;
    }

    protected $presenter = 'App\Ninja\Presenters\BillPresenter';

    protected $table = 'bills';
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
        'bill_status_id',
        'branch_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'has_tasks' => 'boolean',
        'vendor_enable_auto_bill' => 'boolean',
        'auto_bill' => 'boolean',
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
        'bill_date',
        'due_date',
        'po_number',
        'discount',
        'partial',
    ];

    public static $statusClasses = [
        BILL_STATUS_SENT => 'info',
        BILL_STATUS_VIEWED => 'warning',
        BILL_STATUS_APPROVED => 'success',
        BILL_STATUS_PARTIAL => 'primary',
        BILL_STATUS_PAID => 'success',
    ];

    public static function getImportColumns()
    {
        return [
            'name',
            'email',
            'invoice_number',
            'po_number',
            'bill_date',
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
            'bill date|create date' => 'bill_date',
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
        return $this->isType(BILL_TYPE_QUOTE) ? ENTITY_BILL_QUOTE : ENTITY_BILL;
    }

    public function subEntityType()
    {
        if ($this->is_recurring) {
            return ENTITY_RECURRING_BILL;
        } else {
            return $this->getEntityType();
        }
    }

    public function getRoute()
    {
        if ($this->is_recurring) {
            $entityType = 'recurring_bill';
        } else {
            $entityType = $this->getEntityType();
        }

        return "/{$entityType}s/{$this->public_id}";
    }

    public function getDisplayName()
    {
        return $this->is_recurring ? trans('texts.recurring') : $this->invoice_number;
    }


    public function affectsBalance()
    {
        return $this->isType(BILL_TYPE_STANDARD) && !$this->is_recurring && $this->is_public;
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
        // if we've just made the bill public then apply the full amount
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
                         'bill_date',
                         'due_date',
                         'terms',
                         'public_notes',
                         'bill_footer',
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

            unset($dirty['bill_status_id']);
            unset($dirty['vendor_enable_auto_bill']);
            unset($dirty['quote_bill_id']);

            return count($dirty) > 0;
        }
    }

    public function getAmountPaid($calculate = false)
    {
        if ($this->isType(BILL_TYPE_QUOTE) || $this->is_recurring) {
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
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
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

//  to cheat bill items
    public function invoice_items()
    {
        return $this->hasMany('App\Models\BillItem')->orderBy('id');
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

    public function bill_status()
    {
        return $this->belongsTo('App\Models\BillStatus');
    }


    public function invoice_design()
    {
        return $this->belongsTo('App\Models\BillDesign');
    }

    public function bill_payments()
    {
        return $this->hasMany('App\Models\BillPayment');
    }

    public function recurring_bill()
    {
        return $this->belongsTo('App\Models\Bill');
    }

    public function quote()
    {
        return $this->belongsTo('App\Models\Bill')->withTrashed();
    }

    public function recurring_bills()
    {
        return $this->hasMany('App\Models\Bill', 'recurring_bill_id');
    }

    public function frequency()
    {
        return $this->belongsTo('App\Models\Frequency');
    }

    public function bill_invitations()
    {
        return $this->hasMany('App\Models\BillInvitation');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense')->withTrashed();
    }

    public function bill_expenses()
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

    public function scopeBills($query)
    {
        return $query->where('bill_type_id', BILL_TYPE_STANDARD)
            ->where('is_recurring', false);
    }

    public function scopeRecurring($query)
    {
        return $query->where('bill_type_id', BILL_TYPE_STANDARD)
            ->where('is_recurring', true);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('bill_date', [$startDate, $endDate]);
        })->orWhere(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        });
    }

    public function scopeQuotes($query)
    {
        return $query->where('bill_type_id', BILL_TYPE_QUOTE)
            ->where('is_recurring', false);
    }

    public function scopeUnapprovedQuotes($query, $includeBillId = false)
    {
        return $query->quotes()
            ->where(function ($query) use ($includeBillId) {
                $query->where('id', $includeBillId)
                    ->orWhere(function ($query) {
                        $query->where('bill_status_id', '<', BILL_STATUS_APPROVED)
                            ->whereNull('quote_bill_id');
                    });
            });
    }

    public function scopeBillType($query, $typeId)
    {
        return $query->where('bill_type_id', $typeId);
    }

    public function scopeStatusIds($query, $statusIds)
    {
        if (!$statusIds || (is_array($statusIds) && !count($statusIds))) {
            return $query;
        }

        return $query->where(function ($query) use ($statusIds) {
            foreach ($statusIds as $statusId) {
                $query->orWhere('bill_status_id', $statusId);
            }
            if (in_array(BILL_STATUS_UNPAID, $statusIds)) {
                $query->orWhere(function ($query) {
                    $query->where('balance', '>', 0)
                        ->where('is_public', true);
                });
            }
            if (in_array(BILL_STATUS_OVERDUE, $statusIds)) {
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
        return $this->bill_type_id == $typeId;
    }

    public function isQuote()
    {
        return $this->isType(BILL_TYPE_QUOTE);
    }

    public function getCustomMessageType()
    {
        if ($this->isQuote()) {
            return $this->quote_bill_id ? CUSTOM_MESSAGE_APPROVED_QUOTE : CUSTOM_MESSAGE_UNAPPROVED_QUOTE;
        } else {
            return $this->balance > 0 ? CUSTOM_MESSAGE_UNPAID_INVOICE : CUSTOM_MESSAGE_PAID_INVOICE;
        }
    }

    public function isStandard()
    {
        return $this->isType(BILL_TYPE_STANDARD) && !$this->is_recurring;
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
            $this->bill_status_id = BILL_STATUS_SENT;
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

        if (!$this->relationLoaded('bill_invitations')) {
            $this->load('bill_invitations');
        }

        foreach ($this->bill_invitations as $invitation) {
            $this->markInvitationSent($invitation, false, $notify, $reminder);
        }
    }

    public function areInvitationsSent()
    {
        if (!$this->relationLoaded('bill_invitations')) {
            $this->load('bill_invitations');
        }

        foreach ($this->bill_invitations as $invitation) {
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
            $this->bill_status_id = BILL_STATUS_SENT;
            $this->save();
        }

        $invitation->markSent($messageId);

        // if the user marks it as sent rather than actually sending it
        // then we won't track it in the activity log
        if (!$notify) {
            return false;
        }

        if ($this->isType(BILL_TYPE_QUOTE)) {
            event(new BillQuoteInvitationWasEmailedEvent($invitation, $notes));
        } else {
            event(new BillInvitationWasEmailedEvent($invitation, $notes));
        }
    }

    public function markViewed()
    {
        if (!$this->isViewed()) {
            $this->bill_status_id = BILL_STATUS_VIEWED;
            $this->save();
        }
    }

    public function updatePaidStatus($paid = false, $save = true)
    {
        $statusId = false;
        if ($paid && $this->balance == 0) {
            $statusId = BILL_STATUS_PAID;
        } elseif ($paid && $this->balance > 0 && $this->balance < $this->amount) {
            $statusId = BILL_STATUS_PARTIAL;
        } elseif ($this->isPartial() && $this->balance > 0) {
            $statusId = ($this->balance == $this->amount ? BILL_STATUS_SENT : BILL_STATUS_PARTIAL);
        }

        if ($statusId && $statusId != $this->bill_status_id) {
            $this->bill_status_id = $statusId;
            if ($save) {
                $this->save();
            }
        }
    }

    public function markApproved()
    {
        if ($this->isType(BILL_TYPE_QUOTE)) {
            $this->bill_status_id = BILL_STATUS_APPROVED;
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
            if ($billItem = $this->getGatewayFeeItem()) {
                $billItem->markFeePaid();
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

    public static function calcStatusLabel($status, $class, $entityType, $quoteBillId)
    {
        if ($quoteBillId) {
            $label = 'converted';
        } elseif ($class == 'danger') {
            $label = $entityType == ENTITY_BILL ? 'past_due' : 'expired';
        } else {
            $label = 'status_' . strtolower($status);
        }

        return trans("texts.{$label}");
    }

    public static function calcStatusClass($statusId, $balance, $dueDate, $isRecurring)
    {
        if ($statusId >= BILL_STATUS_SENT && !$isRecurring && static::calcIsOverdue($balance, $dueDate)) {
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
        return static::calcStatusClass($this->bill_status_id, $this->balance, $dueDate, $this->is_recurring);
    }

    public function statusLabel()
    {
        return static::calcStatusLabel($this->bill_status->name, $this->statusClass(), $this->getEntityType(), $this->quote_bill_id);
    }

    public static function calcLink($bill)
    {
        if (!empty($bill->bill_type_id)) {
            $linkPrefix = ($bill->bill_type_id == BILL_TYPE_QUOTE) ? 'bill_quotes/' : 'bills/';
        } else {
            $linkPrefix = 'bills/';
        }
        return link_to($linkPrefix . $bill->public_id, $bill->invoice_number);
    }

    public function getLink()
    {
        return self::calcLink($this);
    }

    public function getInvitationLink($type = 'view', $forceOnsite = false, $forcePlain = false)
    {
        if (!$this->relationLoaded('bill_invitations')) {
            $this->load('bill_invitations');
        }

        return $this->bill_invitations[0]->getLink($type, $forceOnsite, $forcePlain);
    }

    public function isSent()
    {
        return $this->bill_status_id >= BILL_STATUS_SENT && $this->getOriginal('is_public');
    }

    public function isViewed()
    {
        return $this->bill_status_id >= BILL_STATUS_VIEWED;
    }

    public function isApproved()
    {
        return $this->bill_status_id >= BILL_STATUS_APPROVED || $this->quote_bill_id;
    }

    public function isPartial()
    {
        return $this->bill_status_id >= BILL_STATUS_PARTIAL;
    }

    public function isPaid()
    {
        return $this->bill_status_id >= BILL_STATUS_PAID;
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
            'product_key',
            'invoice_number',
            'discount',
            'is_amount_discount',
            'po_number',
            'bill_date',
            'due_date',
            'terms',
            'bill_footer',
            'public_notes',
            'amount',
            'balance',
            'invoice_items',
            'documents',
            'expenses',
            'client',
            'bill_invitations',
            'tax_name1',
            'tax_rate1',
            'tax_name2',
            'tax_rate2',
            'account',
            'invoice_design',
            'invoice_design_id',
            'invoice_fonts',
            'features',
            'bill_type_id',
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
            'bill_embed_documents',
            'page_size',
            'include_item_taxes_inline',
            'bill_fields',
            'show_currency_code',
            'inclusive_taxes',
            'date_format',
            'datetime_format',
            'timezone',
            'signature_on_pdf',
        ]);

        foreach ($this->bill_invitations as $invitation) {
            $invitation->setVisible([
                'signature_base64',
                'signature_date',
            ]);
        }

        foreach ($this->invoice_items as $billItem) {
            $billItem->setVisible([
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
                'bill_item_type_id',
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

    public function getDueDate($bill_date = null)
    {
        if (!$this->is_recurring) {
            return $this->due_date ? $this->due_date : null;
        } else {
            $now = time();
            if ($bill_date) {
                // If $bill_date is specified, all calculations are based on that date
                if (is_numeric($bill_date)) {
                    $now = strtotime($bill_date);
                } elseif (is_string($bill_date)) {
                    $now = strtotime($bill_date);
                } elseif ($bill_date instanceof DateTime) {
                    $now = $bill_date->getTimestamp();
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

        $invitation = $invitation ?: $this->bill_invitations[0];
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

    public function getItemTaxable($billItem, $billTotal)
    {
        $total = $billItem->qty * $billItem->cost;

        if ($this->discount != 0) {
            if ($this->is_amount_discount) {
                $total -= $billTotal ? ($total / ($billTotal + $this->discount) * $this->discount) : 0;
            } else {
                $total *= (100 - $this->discount) / 100;
            }
        }

        if ($billItem->discount != 0) {
            if ($this->is_amount_discount) {
                $total -= $billItem->discount;
            } else {
                $total -= $total * $billItem->discount / 100;
            }
        }

        return round($total, 2);
    }

    public function getTaxable()
    {
        $total = 0;

        foreach ($this->invoice_items as $billItem) {
            $lineTotal = $billItem->qty * $billItem->cost;

            if ($billItem->discount != 0) {
                if ($this->is_amount_discount) {
                    $lineTotal -= $billItem->discount;
                } else {
                    $lineTotal -= $lineTotal * $billItem->discount / 100;
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
            $billTaxAmount = $this->taxAmount($taxable, $this->tax_rate1);
            $billPaidAmount = floatval($this->amount) && $billTaxAmount ? ($paidAmount / $this->amount * $billTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name1, $this->tax_rate1, $billTaxAmount, $billPaidAmount);
        }
        if ($this->tax_name2) {
            $billTaxAmount = $this->taxAmount($taxable, $this->tax_rate2);
            $billPaidAmount = floatval($this->amount) && $billTaxAmount ? ($paidAmount / $this->amount * $billTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name2, $this->tax_rate2, $billTaxAmount, $billPaidAmount);
        }

        foreach ($this->invoice_items as $billItem) {
            $itemTaxable = $this->getItemTaxable($billItem, $taxable);

            if ($billItem->tax_name1) {
                $itemTaxAmount = $this->taxAmount($itemTaxable, $billItem->tax_rate1);
                $itemPaidAmount = floatval($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $billItem->tax_name1, $billItem->tax_rate1, $itemTaxAmount, $itemPaidAmount);
            }
            if ($billItem->tax_name2) {
                $itemTaxAmount = $this->taxAmount($itemTaxable, $billItem->tax_rate2);
                $itemPaidAmount = floatval($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $billItem->tax_name2, $billItem->tax_rate2, $itemTaxAmount, $itemPaidAmount);
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
            $recurBill = $this->recurring_bill;
        } else {
            $recurBill = $this;
        }

        if (!$recurBill) {
            return false;
        }

        return $recurBill->auto_bill == AUTO_BILL_ALWAYS || ($recurBill->auto_bill != AUTO_BILL_OFF && $recurBill->vendor_enable_auto_bill);
    }

    public static function getStatuses($entityType = false)
    {
        $statuses = [];

        if ($entityType == ENTITY_RECURRING_BILL) {
            return $statuses;
        }

        foreach (Cache::get('invoiceStatus') as $status) {
            if ($entityType == ENTITY_BILL_QUOTE) {
                if (in_array($status->id, [BILL_STATUS_PAID, BILL_STATUS_PARTIAL])) {
                    continue;
                }
            } elseif ($entityType == ENTITY_BILL) {
                if (in_array($status->id, [BILL_STATUS_APPROVED])) {
                    continue;
                }
            }

            $statuses[$status->id] = trans('texts.status_' . strtolower($status->name));
        }

        if ($entityType == ENTITY_BILL) {
            $statuses[BILL_STATUS_UNPAID] = trans('texts.unpaid');
            $statuses[BILL_STATUS_OVERDUE] = trans('texts.past_due');
        }

        return $statuses;
    }

    public function emailHistory()
    {
        return Activity::scope()
            ->with(['vendor_contact'])
            ->where('bill_id', $this->id)
            ->whereIn('activity_type_id', [ACTIVITY_TYPE_EMAIL_BILL, ACTIVITY_TYPE_EMAIL_BILL_QUOTE])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getDateLabel()
    {
        return $this->getEntityType() === ENTITY_BILL ? 'bill_date' : 'quote_date';
    }

    public function getDueDateLabel()
    {
        return $this->isQuote() ? 'valid_until' : 'due_date';
    }

    public function onlyHasTasks()
    {
        foreach ($this->invoice_items as $item) {
            if ($item->bill_item_type_id != BILL_ITEM_TYPE_TASK) {
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
        if (!$lockSentBill = config('ninja.lock_sent_bills')) {
            return false;
        }

        if ($this->is_recurring) {
            return false;
        }

    }

    public function getBillLinkForQuote($vendorContactId)
    {
        if (!$this->quote_bill_id) {
            return false;
        }

        $bill = static::scope($this->quote_bill_id, $this->account_id)
            ->with('bill_invitations')->first();

        if (!$bill) {
            return false;
        }

        foreach ($bill->bill_invitations as $invitation) {
            if ($invitation->contact_id == $vendorContactId) {
                return $invitation->getLink();
            }
        }

        return false;
    }
}

// Bill counter maybe not required for bill quote case
Bill::creating(function ($bill) {
    if (!$bill->is_recurring) {
        $account = $bill->account;
        if ($bill->amount >= 0) {
            $account->vendorIncrementCounter($bill);
        } elseif ($account->credit_number_counter > 0) {
            $account->vendorIncrementCounter(new BillCredit());
        }
    }
});

Bill::created(function ($bill) {
    if ($bill->isType(BILL_TYPE_QUOTE)) {
        event(new BillQuoteWasCreatedEvent($bill));
    } else {
        event(new BillWasCreatedEvent($bill));
    }
});

Bill::updating(function ($bill) {
    if ($bill->isType(BILL_TYPE_QUOTE)) {
        event(new BillQuoteWasUpdatedEvent($bill));
    } else {
        event(new BillWasUpdatedEvent($bill));
    }
});
