<?php

namespace App\Models;

use App\Events\Sale\InvoiceInvitationWasEmailedEvent;
use App\Events\Sale\InvoiceWasCreatedEvent;
use App\Events\Sale\InvoiceWasUpdatedEvent;
use App\Events\Sale\QuoteInvitationWasEmailedEvent;
use App\Events\Sale\QuoteWasCreatedEvent;
use App\Events\Sale\QuoteWasUpdatedEvent;
use App\Libraries\CurlUtils;
use App\Libraries\Utils;
use App\Models\Traits\ChargesFees;
use App\Models\Traits\HasRecurrence;
use App\Models\Traits\OwnedByClientTrait;
use DateTime;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Invoice.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $client_id
 * @property int|null $user_id
 * @property int|null $recurring_invoice_id
 * @property int|null $invoice_status_id
 * @property int|null $branch_id
 * @property int|null $invoice_design_id
 * @property int|null $invoice_type_id
 * @property int|null $quote_id
 * @property int|null $quote_invoice_id
 * @property string|null $invoice_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $po_number
 * @property string|null $invoice_date
 * @property string|null $due_date
 * @property string|null $terms
 * @property string|null $public_notes
 * @property int|null $is_deleted
 * @property bool|null $is_recurring
 * @property int|null $frequency_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $last_sent_date
 * @property string|null $tax_name1
 * @property float|null $tax_rate1
 * @property float|null $amount
 * @property float|null $balance
 * @property float|null $discount
 * @property int|null $is_amount_discount
 * @property float|null $custom_value1
 * @property float|null $custom_value2
 * @property int|null $custom_taxes1
 * @property int|null $custom_taxes2
 * @property string|null $invoice_footer
 * @property float|null $partial
 * @property bool|null $has_tasks
 * @property int|null $auto_bill
 * @property string|null $custom_text_value1
 * @property string|null $custom_text_value2
 * @property bool|null $has_expenses
 * @property string|null $tax_name2
 * @property float|null $tax_rate2
 * @property bool|null $client_enable_auto_bill
 * @property int|null $is_public
 * @property string|null $private_notes
 * @property string|null $partial_due_date
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Branch|null $branch
 * @property-read Client|null $client
 * @property-read Collection|Document[] $documents
 * @property-read int|null $documents_count
 * @property-read Collection|Expense[] $expenses
 * @property-read int|null $expenses_count
 * @property-read Frequency|null $frequency
 * @property-read Collection|Invitation[] $invitations
 * @property-read int|null $invitations_count
 * @property-read InvoiceDesign|null $invoice_design
 * @property-read Collection|InvoiceItem[] $invoice_items
 * @property-read int|null $invoice_items_count
 * @property-read InvoiceStatus|null $invoice_status
 * @property-read Collection|Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read Invoice|null $quote
 * @property-read Invoice|null $recurring_invoice
 * @property-read Collection|Invoice[] $recurring_invoices
 * @property-read int|null $recurring_invoices_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice invoiceType($typeId)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice invoices()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static Builder|Invoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice quotes()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice recurring()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice statusIds($statusIds)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice unapprovedQuotes($includeInvoiceId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereAutoBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientEnableAutoBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCustomTaxes1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCustomTaxes2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCustomTextValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCustomTextValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereFrequencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereHasExpenses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereHasTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereIsAmountDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereIsRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereLastSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePartial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePartialDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePoNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereQuoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereQuoteInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereRecurringInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Invoice withTrashed()
 * @method static Builder|Invoice withoutTrashed()
 * @mixin Eloquent
 */
class Invoice extends EntityModel implements BalanceAffecting
{
    use PresentableTrait;
    use OwnedByClientTrait;
    use ChargesFees;
    use HasRecurrence;

    use SoftDeletes {
        SoftDeletes::trashed as parentTrashed;
    }

    protected $presenter = 'App\Ninja\Presenters\InvoicePresenter';

    protected $table = 'invoices';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['deleted_at'];


    protected $fillable = [
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'private_notes',
        'last_sent_date',
        'invoice_design_id',
        'branch_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'has_tasks' => 'boolean',
        'client_enable_auto_bill' => 'boolean',
        'has_expenses' => 'boolean',
    ];

    public static $patternFields = [
        'counter',
        'clientCounter',
        'clientIdNumber',
        'clientCustom1',
        'clientCustom2',
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
            'client|organization' => 'name',
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
        return $this->isType(INVOICE_TYPE_QUOTE) ? ENTITY_QUOTE : ENTITY_INVOICE;
    }

    public function subEntityType()
    {
        if ($this->is_recurring) {
            return ENTITY_RECURRING_INVOICE;
        } else {
            return $this->getEntityType();
        }
    }

    public function getRoute()
    {
        if ($this->is_recurring) {
            $entityType = 'recurring_invoice';
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
        return $this->isType(INVOICE_TYPE_STANDARD) && !$this->is_recurring && $this->is_public;
    }

    public function getAdjustment()
    {
        if (!$this->affectsBalance()) {
            return 0;
        }

        return $this->getRawAdjustment();
    }

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
            unset($dirty['client_enable_auto_bill']);
            unset($dirty['quote_invoice_id']);

            return count($dirty) > 0;
        }
    }

    public function getAmountPaid($calculate = false)
    {
        if ($this->isType(INVOICE_TYPE_QUOTE) || $this->is_recurring) {
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
        if ($this->client && $this->client->trashed()) {
            return true;
        }

        return self::parentTrashed();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    public function invoice_items()
    {
        return $this->hasMany('App\Models\InvoiceItem')->orderBy('id');
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
        return $this->belongsTo('App\Models\InvoiceDesign');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function recurring_invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    public function quote()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function recurring_invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'recurring_invoice_id');
    }

    public function frequency()
    {
        return $this->belongsTo('App\Models\Frequency');
    }

    public function invitations()
    {
        return $this->hasMany('App\Models\Invitation')->orderBy('invitations.contact_id');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch')->withTrashed();
    }

    public function scopeInvoices($query)
    {
        return $query->where('invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('is_recurring', false);
    }

    public function scopeRecurring($query)
    {
        return $query->where('invoice_type_id', INVOICE_TYPE_STANDARD)
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
        return $query->where('invoice_type_id', INVOICE_TYPE_QUOTE)
            ->where('is_recurring', false);
    }

    public function scopeUnapprovedQuotes($query, $includeInvoiceId = false)
    {
        return $query->quotes()
            ->where(function ($query) use ($includeInvoiceId) {
                $query->whereId($includeInvoiceId)
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
        return $this->isType(INVOICE_TYPE_QUOTE);
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
        return $this->isType(INVOICE_TYPE_STANDARD) && !$this->is_recurring;
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
            return;
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
            return;
        }

        if (!$this->relationLoaded('invitations')) {
            $this->load('invitations');
        }

        foreach ($this->invitations as $invitation) {
            $this->markInvitationSent($invitation, false, $notify, $reminder);
        }
    }

    public function areInvitationsSent()
    {
        if (!$this->relationLoaded('invitations')) {
            $this->load('invitations');
        }

        foreach ($this->invitations as $invitation) {
            if (!$invitation->isSent()) {
                return false;
            }
        }

        return true;
    }

    public function markInvitationSent($invitation, $messageId = false, $notify = true, $notes = false)
    {
        if ($this->is_deleted) {
            return;
        }

        if (!$this->isSent()) {
            $this->is_public = true;
            $this->invoice_status_id = INVOICE_STATUS_SENT;
            $this->save();
        }

        $invitation->markSent($messageId);

        // if the user marks it as sent rather than acually sending it
        // then we won't track it in the activity log
        if (!$notify) {
            return;
        }

        if ($this->isType(INVOICE_TYPE_QUOTE)) {
            event(new QuoteInvitationWasEmailedEvent($invitation, $notes));
        } else {
            event(new InvoiceInvitationWasEmailedEvent($invitation, $notes));
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
        if ($this->isType(INVOICE_TYPE_QUOTE)) {
            $this->invoice_status_id = INVOICE_STATUS_APPROVED;
            $this->save();
        }
    }

    public function updateBalances($balanceAdjustment, $partial = 0)
    {
        if ($this->is_deleted) {
            return;
        }

        $balanceAdjustment = floatval($balanceAdjustment);
        $partial = floatval($partial);

        if (!$balanceAdjustment && $this->partial == $partial) {
            return;
        }

        $this->balance = $this->balance + $balanceAdjustment;

        if ($this->partial > 0) {
            $this->partial = $partial;

            // clear the partial due date and set the due date
            // using payment terms if it's blank
            if (!$this->partial && $this->partial_due_date) {
                $this->partial_due_date = null;
                if (!$this->due_date) {
                    $this->due_date = $this->account->defaultDueDate($this->client);
                }
            }
        }

        $this->save();

        // mark fees as paid
        if ($balanceAdjustment != 0 && $this->account->gateway_fee_enabled) {
            if ($invoiceItem = $this->getGatewayFeeItem()) {
                $invoiceItem->markFeePaid();
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

    public static function calcStatusLabel($status, $class, $entityType, $quoteInvoiceId)
    {
        if ($quoteInvoiceId) {
            $label = 'converted';
        } elseif ($class == 'danger') {
            $label = $entityType == ENTITY_INVOICE ? 'past_due' : 'expired';
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

    public static function calcLink($invoice)
    {
        if (!empty($invoice->invoice_type_id)) {
            $linkPrefix = ($invoice->invoice_type_id == INVOICE_TYPE_QUOTE) ? 'quotes/' : 'invoices/';
        } else {
            $linkPrefix = 'invoices/';
        }
        return link_to($linkPrefix . $invoice->public_id, $invoice->invoice_number);
    }

    public function getLink()
    {
        return self::calcLink($this);
    }

    public function getInvitationLink($type = 'view', $forceOnsite = false, $forcePlain = false)
    {
        if (!$this->relationLoaded('invitations')) {
            $this->load('invitations');
        }

        return $this->invitations[0]->getLink($type, $forceOnsite, $forcePlain);
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
        if ($this->client->currency) {
            return $this->client->currency->code;
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
            'client',
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

        $this->client->setVisible([
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

        foreach ($this->invoice_items as $invoiceItem) {
            $invoiceItem->setVisible([
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

        foreach ($this->client->contacts as $contact) {
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

    public function getDueDate($invoice_date = null)
    {
        if (!$this->is_recurring) {
            return $this->due_date ? $this->due_date : null;
        } else {
            $now = time();
            if ($invoice_date) {
                // If $invoice_date is specified, all calculations are based on that date
                if (is_numeric($invoice_date)) {
                    $now = $invoice_date;
                } elseif (is_string($invoice_date)) {
                    $now = strtotime($invoice_date);
                } elseif ($invoice_date instanceof DateTime) {
                    $now = $invoice_date->getTimestamp();
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
            } elseif ($this->client->payment_terms != 0) {
                // No custom due date set for this invoice; use the client's payment terms
                $days = $this->client->defaultDaysDue();

                return date('Y-m-d', strtotime('+' . $days . ' day', $now));
            } elseif ($this->account->payment_terms != 0) {
                $days = $this->account->defaultDaysDue();

                return date('Y-m-d', strtotime('+' . $days . ' day', $now));
            } elseif ($this->account->payment_terms != 0) {
                // No custom due date set for this invoice; use the client's payment terms
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

        $invitation = $invitation ?: $this->invitations[0];
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

    public function getItemTaxable($invoiceItem, $invoiceTotal)
    {
        $total = $invoiceItem->qty * $invoiceItem->cost;

        if ($this->discount != 0) {
            if ($this->is_amount_discount) {
                $total -= $invoiceTotal ? ($total / ($invoiceTotal + $this->discount) * $this->discount) : 0;
            } else {
                $total *= (100 - $this->discount) / 100;
            }
        }

        if ($invoiceItem->discount != 0) {
            if ($this->is_amount_discount) {
                $total -= $invoiceItem->discount;
            } else {
                $total -= $total * $invoiceItem->discount / 100;
            }
        }

        return round($total, 2);
    }

    public function getTaxable()
    {
        $total = 0;

        foreach ($this->invoice_items as $invoiceItem) {
            $lineTotal = $invoiceItem->qty * $invoiceItem->cost;

            if ($invoiceItem->discount != 0) {
                if ($this->is_amount_discount) {
                    $lineTotal -= $invoiceItem->discount;
                } else {
                    $lineTotal -= $lineTotal * $invoiceItem->discount / 100;
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
            $invoiceTaxAmount = $this->taxAmount($taxable, $this->tax_rate1);
            $invoicePaidAmount = floatval($this->amount) && $invoiceTaxAmount ? ($paidAmount / $this->amount * $invoiceTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name1, $this->tax_rate1, $invoiceTaxAmount, $invoicePaidAmount);
        }
        if ($this->tax_name2) {
            $invoiceTaxAmount = $this->taxAmount($taxable, $this->tax_rate2);
            $invoicePaidAmount = floatval($this->amount) && $invoiceTaxAmount ? ($paidAmount / $this->amount * $invoiceTaxAmount) : 0;
            $this->calculateTax($taxes, $this->tax_name2, $this->tax_rate2, $invoiceTaxAmount, $invoicePaidAmount);
        }

        foreach ($this->invoice_items as $invoiceItem) {
            $itemTaxable = $this->getItemTaxable($invoiceItem, $taxable);

            if ($invoiceItem->tax_name1) {
                $itemTaxAmount = $this->taxAmount($itemTaxable, $invoiceItem->tax_rate1);
                $itemPaidAmount = floatval($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $invoiceItem->tax_name1, $invoiceItem->tax_rate1, $itemTaxAmount, $itemPaidAmount);
            }
            if ($invoiceItem->tax_name2) {
                $itemTaxAmount = $this->taxAmount($itemTaxable, $invoiceItem->tax_rate2);
                $itemPaidAmount = floatval($this->amount) && $itemTaxAmount ? ($paidAmount / $this->amount * $itemTaxAmount) : 0;
                $this->calculateTax($taxes, $invoiceItem->tax_name2, $invoiceItem->tax_rate2, $itemTaxAmount, $itemPaidAmount);
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
            return;
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
            $recurInvoice = $this->recurring_invoice;
        } else {
            $recurInvoice = $this;
        }

        if (!$recurInvoice) {
            return false;
        }

        return $recurInvoice->auto_bill == AUTO_BILL_ALWAYS || ($recurInvoice->auto_bill != AUTO_BILL_OFF && $recurInvoice->client_enable_auto_bill);
    }

    /**
     * @param bool $entityType
     * @return array
     */
    public static function getStatuses($entityType = false)
    {
        $statuses = [];

        if ($entityType == ENTITY_RECURRING_INVOICE) {
            return $statuses;
        }

        foreach (Cache::get('invoiceStatus') as $status) {
            if ($entityType == ENTITY_QUOTE) {
                if (in_array($status->id, [INVOICE_STATUS_PAID, INVOICE_STATUS_PARTIAL])) {
                    continue;
                }
            } elseif ($entityType == ENTITY_INVOICE) {
                if (in_array($status->id, [INVOICE_STATUS_APPROVED])) {
                    continue;
                }
            }

            $statuses[$status->id] = trans('texts.status_' . strtolower($status->name));
        }

        if ($entityType == ENTITY_INVOICE) {
            $statuses[INVOICE_STATUS_UNPAID] = trans('texts.unpaid');
            $statuses[INVOICE_STATUS_OVERDUE] = trans('texts.past_due');
        }

        return $statuses;
    }

    public function emailHistory()
    {
        return Activity::scope()
            ->with(['contact'])
            ->where('invoice_id', $this->id)
            ->whereIn('activity_type_id', [ACTIVITY_TYPE_EMAIL_INVOICE, ACTIVITY_TYPE_EMAIL_QUOTE])
            ->orderBy('id', 'desc')
            ->get();
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
        if (!$lockSentInvoice = config('ninja.lock_sent_invoices')) {
            return false;
        }

        if ($this->is_recurring) {
            return false;
        }

    }

    public function getInvoiceLinkForQuote($contactId)
    {
        if (!$this->quote_invoice_id) {
            return false;
        }

        $invoice = static::scope($this->quote_invoice_id, $this->account_id)
            ->with('invitations')->first();

        if (!$invoice) {
            return false;
        }

        foreach ($invoice->invitations as $invitation) {
            if ($invitation->contact_id == $contactId) {
                return $invitation->getLink();
            }
        }

        return false;
    }
}

Invoice::creating(function ($invoice) {
    if (!$invoice->is_recurring) {
        $account = $invoice->account;
        if ($invoice->amount >= 0) {
            $account->clientIncrementCounter($invoice);
        } elseif ($account->credit_number_counter > 0) {
            $account->clientIncrementCounter(new Credit());
        }
    }
});

Invoice::created(function ($invoice) {
    if ($invoice->isType(INVOICE_TYPE_QUOTE)) {
        event(new QuoteWasCreatedEvent($invoice));
    } else {
        event(new InvoiceWasCreatedEvent($invoice));
    }
});

Invoice::updating(function ($invoice) {
    if ($invoice->isType(INVOICE_TYPE_QUOTE)) {
        event(new QuoteWasUpdatedEvent($invoice));
    } else {
        event(new InvoiceWasUpdatedEvent($invoice));
    }
});
