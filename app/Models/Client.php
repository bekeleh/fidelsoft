<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use App\Libraries\Utils;
use App\Models\Traits\HasCustomMessages;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\PresentableTrait;
use Carbon\Carbon;

/**
 * Model Class Client.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $currency_id
 * @property int|null $client_type_id
 * @property int|null $sale_type_id
 * @property int|null $hold_reason_id
 * @property int|null $country_id
 * @property int|null $industry_id
 * @property int|null $size_id
 * @property int|null $language_id
 * @property int|null $shipping_country_id
 * @property string|null $name
 * @property string|null $id_number
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $work_phone
 * @property float $balance
 * @property float $paid_to_date
 * @property string|null $last_login
 * @property string|null $website
 * @property int $is_deleted
 * @property int|null $payment_terms
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $vat_number
 * @property int $invoice_number_counter
 * @property int $quote_number_counter
 * @property int $credit_number_counter
 * @property float $task_rate
 * @property string|null $private_notes
 * @property string|null $public_notes
 * @property string|null $shipping_address1
 * @property string|null $shipping_address2
 * @property string|null $shipping_city
 * @property string|null $shipping_state
 * @property string|null $shipping_postal_code
 * @property string|null $billing_address1
 * @property string|null $billing_address2
 * @property string|null $billing_city
 * @property string|null $billing_state
 * @property string|null $billing_postal_code
 * @property int $show_tasks_in_portal
 * @property int $send_reminders
 * @property string|null $custom_messages
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read ClientType|null $clientType
 * @property-read Collection|Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read Country|null $country
 * @property-read Collection|Credit[] $credits
 * @property-read int|null $credits_count
 * @property-read Collection|Credit[] $creditsWithBalance
 * @property-read int|null $credits_with_balance_count
 * @property-read Currency|null $currency
 * @property-read Collection|Expense[] $expenses
 * @property-read int|null $expenses_count
 * @property-read HoldReason|null $holdReason
 * @property-read Industry|null $industry
 * @property-read Collection|Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read Language|null $language
 * @property-read Collection|Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read Collection|Invoice[] $publicQuotes
 * @property-read int|null $public_quotes_count
 * @property-read Collection|Invoice[] $quotes
 * @property-read int|null $quotes_count
 * @property-read SaleType|null $saleType
 * @property-read Country|null $shipping_country
 * @property-read Size|null $size
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Client isInvoiceAllowed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static Builder|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBillingAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBillingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBillingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBillingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBillingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreditNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCustomMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereHoldReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereInvoiceNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePaidToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereQuoteNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSaleTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSendReminders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShippingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShowTasksInPortal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereTaskRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereWorkPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Client withTrashed()
 * @method static Builder|Client withoutTrashed()
 * @mixin Eloquent
 */
class Client extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;
    use HasCustomMessages;

    protected $presenter = 'App\Ninja\Presenters\ClientPresenter';

    protected $table = 'clients';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $hidden = ['deleted_at'];


    protected $fillable = [
        'name',
        'id_number',
        'vat_number',
        'work_phone',
        'custom_value1',
        'custom_value2',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'private_notes',
        'size_id',
        'industry_id',
        'currency_id',
        'language_id',
        'client_type_id',
        'sale_type_id',
        'hold_reason_id',
        'payment_terms',
        'website',
        'invoice_number_counter',
        'quote_number_counter',
        'public_notes',
        'task_rate',
        'shipping_address1',
        'shipping_address2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country_id',
        'show_tasks_in_portal',
        'send_reminders',
        'custom_messages',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public static function getImportColumns()
    {
        return [
            'name',
            'work_phone',
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'public_notes',
            'private_notes',
            'country',
            'website',
            'currency',
            'vat_number',
            'id_number',
            'custom1',
            'custom2',
            'contact_first_name',
            'contact_last_name',
            'contact_phone',
            'contact_email',
            'contact_custom1',
            'contact_custom2',
        ];
    }


    public static function getImportMap()
    {
        return [
            'first' => 'contact_first_name',
            'last^last4' => 'contact_last_name',
            'email' => 'contact_email',
            'work|office' => 'work_phone',
            'mobile|phone' => 'contact_phone',
            'name|organization|description^card' => 'name',
            'apt|street2|address2|line2' => 'address2',
            'street|address1|line1^avs' => 'address1',
            'city' => 'city',
            'state|province' => 'state',
            'zip|postal|code^avs' => 'postal_code',
            'country' => 'country',
            'public' => 'public_notes',
            'private|note' => 'private_notes',
            'site|website' => 'website',
            'currency' => 'currency',
            'vat' => 'vat_number',
            'number' => 'id_number',
        ];
    }

    public function getEntityType()
    {
        return ENTITY_CLIENT;
    }

    public function getRoute()
    {
        return "/clients/{$this->public_id}/edit";
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }


    public function quotes()
    {
        return $this->hasMany('App\Models\Invoice')
            ->where('invoice_type_id', INVOICE_TYPE_QUOTE);
    }


    public function publicQuotes()
    {
        return $this->hasMany('App\Models\Invoice')
            ->where('invoice_type_id', INVOICE_TYPE_QUOTE)
            ->where('is_public', true);
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment');
    }


    public function contacts()
    {
        return $this->hasMany('App\Models\Contact');
    }


    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }


    public function shipping_country()
    {
        return $this->belongsTo('App\Models\Country');
    }


    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }


    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }


    public function size()
    {
        return $this->belongsTo('App\Models\Size');
    }

    public function clientType()
    {
        return $this->belongsTo('App\Models\Setting\ClientType');
    }

    public function saleType()
    {
        return $this->belongsTo('App\Models\Setting\SaleType');
    }

    public function holdReason()
    {
        return $this->belongsTo('App\Models\HoldReason');
    }


    public function industry()
    {
        return $this->belongsTo('App\Models\Industry');
    }


    public function credits()
    {
        return $this->hasMany('App\Models\Credit');
    }


    public function creditsWithBalance()
    {
        return $this->hasMany('App\Models\Credit')
            ->where('balance', '>', 0);
    }


    public function expenses()
    {
        return $this->hasMany('App\Models\Expense');
    }


    public function activities()
    {
        return $this->hasMany('App\Models\Activity')->orderBy('id', 'desc');
    }


    public function addContact($data, $isPrimary = false)
    {
        $publicId = isset($data['public_id'])
            ? $data['public_id'] : (isset($data['id']) ? $data['id'] : false);

        // check if this client wasRecentlyCreated to ensure a new contact is
        // always created even if the request includes a contact id
        if (!$this->wasRecentlyCreated && $publicId && intval($publicId) > 0) {
            $contact = Contact::scope($publicId)->where('client_id', $this->id)->firstOrFail();
            $contact->updated_by = Auth::user()->username;
        } else {
            $contact = Contact::createNew();
            $contact->send_invoice = true;
            $contact->created_by = Auth::user()->username;
            if (isset($data['contact_key']) && $this->account->account_key == env('NINJA_LICENSE_ACCOUNT_KEY')) {
                $contact->contact_key = $data['contact_key'];
            } else {
                $contact->contact_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            }
        }

        if ($this->account->isClientPortalPasswordEnabled()) {
            if (!empty($data['password']) && $data['password'] != '-%unchanged%-') {
                $contact->password = bcrypt($data['password']);
            } elseif (empty($data['password'])) {
                $contact->password = null;
            }
        }

        $contact->fill($data);
        $contact->first_name = isset($data['first_name']) ? trim($data['first_name']) : '';
        $contact->last_name = isset($data['last_name']) ? trim($data['last_name']) : '';
        $contact->is_primary = $isPrimary;
        $contact->email = trim($contact->email);

        return $this->contacts()->save($contact);
    }


    public function updateBalances($balanceAdjustment, $paidToDateAdjustment)
    {
        if ($balanceAdjustment === 0 && $paidToDateAdjustment === 0) {
            return;
        }

        $this->balance = $this->balance + $balanceAdjustment;
        $this->paid_to_date = $this->paid_to_date + $paidToDateAdjustment;

        $this->save();
    }

    public function getTotalCredit()
    {
        return DB::table('credits')
            ->where('client_id', $this->id)
            ->whereNull('deleted_at')
            ->sum('balance');
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrimaryContact()
    {
        if (!$this->relationLoaded('contacts')) {
            $this->load('contacts');
        }

        foreach ($this->contacts as $contact) {
            if ($contact->is_primary) {
                return $contact;
            }
        }

        return false;
    }

    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        } else if ($contact = $this->getPrimaryContact()) {
            return $contact->getDisplayName();
        }

        return false;
    }

    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;

        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    public function showMap()
    {
        return $this->hasAddress() && env('GOOGLE_MAPS_ENABLED') !== false;
    }

    public function addressesMatch()
    {
        $fields = [
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'country_id',
        ];

        foreach ($fields as $field) {
            if ($this->$field != $this->{'shipping_' . $field}) {
                return false;
            }
        }

        return true;
    }

    public function hasAddress($shipping = false)
    {
        $fields = [
            'address1',
            'address2',
            'city',
            'state',
            'postal_code',
            'country_id',
        ];

        foreach ($fields as $field) {
            if ($shipping) {
                $field = 'shipping_' . $field;
            }
            if ($this->$field) {
                return true;
            }
        }

        return false;
    }

    public function getDateCreated()
    {
        if ($this->created_at === '0000-00-00 00:00:00') {
            return '---';
        } else {
            return $this->created_at->format('m/d/y h:i a');
        }
    }

    public function getGatewayToken()
    {
        $accountGateway = $this->account->getGatewayByType(GATEWAY_TYPE_TOKEN);

        if (!$accountGateway) {
            return false;
        }

        return AccountGatewayToken::clientAndGateway($this->id, $accountGateway->id)->first();
    }

    public function defaultPaymentMethod()
    {
        if ($token = $this->getGatewayToken()) {
            return $token->default_payment_method;
        }

        return false;
    }


    public function autoBillLater()
    {
        if ($token = $this->getGatewayToken()) {
            if ($this->account->auto_bill_on_due_date) {
                return true;
            }

            return $token->autoBillLater();
        }

        return false;
    }

    public function getAmount()
    {
        return $this->balance + $this->paid_to_date;
    }

    public function getCurrencyId()
    {
        if ($this->currency_id) {
            return $this->currency_id;
        }

        if (!$this->account) {
            $this->load('account');
        }

        return $this->account->currency_id ?: DEFAULT_CURRENCY;
    }

    public function getCurrencyCode()
    {
        if ($this->currency) {
            return $this->currency->code;
        }

        if (!$this->account) {
            $this->load('account');
        }

        return $this->account->currency ? $this->account->currency->code : 'USD';
    }

    public function getCountryCode()
    {
        if ($country = $this->country) {
            return $country->iso_3166_2;
        }

        if (!$this->account) {
            $this->load('account');
        }

        return $this->account->country ? $this->account->country->iso_3166_2 : 'US';
    }

    public function getClientCounter($isQuote)
    {
        return $isQuote ? $this->quote_number_counter : $this->invoice_number_counter;
    }

    public function markLoggedIn()
    {
        $this->last_login = Carbon::now()->toDateTimeString();
        $this->save();
    }

    public function hasAutoBillConfigurableInvoices()
    {
        return $this->invoices()->where('is_public', true)->whereIn('auto_bill', [AUTO_BILL_OPT_IN, AUTO_BILL_OPT_OUT])->count() > 0;
    }

    public function hasRecurringInvoices()
    {
        return $this->invoices()->where('is_public', true)->where('is_recurring', true)->count() > 0;
    }

    public function defaultDaysDue()
    {
        return $this->payment_terms === -1 ? 0 : $this->payment_terms;
    }

    public function firstInvitationKey()
    {
        if ($invoice = $this->invoices->first()) {
            if ($invitation = $invoice->invitations->first()) {
                return $invitation->invitation_key;
            }
        }
    }

    public function scopeIsInvoiceAllowed($query)
    {
        return $query->whereHas('holdReason', function ($query) {
            $query->where('allow_invoice', 1);
        });
    }
}

Client::creating(function ($client) {
    $client->setNullValues();
    $client->account->clientIncrementCounter($client);
});

Client::updating(function ($client) {
    $client->setNullValues();
});
