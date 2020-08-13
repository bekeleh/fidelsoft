<?php

namespace App\Models;

use App;
use App\Events\UserSettingsChanged;
use App\Libraries\Utils;
use App\Models\Traits\GeneratesNumbers;
use App\Models\Traits\GenerateVendorNumbers;
use App\Models\Traits\HasCustomMessages;
use App\Models\Traits\HasLogo;
use App\Models\Traits\PresentsInvoice;
use App\Models\Traits\SendsEmails;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Laracasts\Presenter\PresentableTrait;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

/**
 * Class Account Model.
 */
class Account extends Eloquent
{
    use PresentableTrait;
    use SoftDeletes;
    use PresentsInvoice;
    use GeneratesNumbers;
    use GenerateVendorNumbers;
    use SendsEmails;
    use HasLogo;
    use HasCustomMessages;


    protected $presenter = 'App\Ninja\Presenters\AccountPresenter';


    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $hidden = ['ip'];


    protected $fillable = [
        'timezone_id',
        'date_format_id',
        'datetime_format_id',
        'currency_id',
        'name',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'invoice_terms',
        'email_footer',
        'industry_id',
        'size_id',
        'invoice_taxes',
        'invoice_item_taxes',
        'invoice_design_id',
        'quote_design_id',
        'work_phone',
        'work_email',
        'language_id',
        'fill_products',
        'update_products',
        'primary_color',
        'secondary_color',
        'hide_quantity',
        'hide_paid_to_date',
        'vat_number',
//      client counter
        'invoice_number_prefix',
        'invoice_number_counter',
        'quote_number_prefix',
        'quote_number_counter',
        'share_counter',
//      bill counter
        'BILL_number_prefix',
        'invoice_number_counter',
        'BILL_QUOTE_number_prefix',
        'quote_number_counter',
        'share_bill_counter',
        'id_number',
        'token_billing_type_id',
        'invoice_footer',
        'pdf_email_attachment',
        'font_size',
        'invoice_labels',
        'custom_design1',
        'custom_design2',
        'custom_design3',
        'show_item_taxes',
        'military_time',
        'enable_reminder1',
        'enable_reminder2',
        'enable_reminder3',
        'enable_reminder4',
        'num_days_reminder1',
        'num_days_reminder2',
        'num_days_reminder3',
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'recurring_hour',
        'invoice_number_pattern',
        'quote_number_pattern',
        'quote_terms',
        'email_design_id',
        'enable_email_markup',
        'website',
        'direction_reminder1',
        'direction_reminder2',
        'direction_reminder3',
        'field_reminder1',
        'field_reminder2',
        'field_reminder3',
        'header_font_id',
        'body_font_id',
        'auto_convert_quote',
        'auto_archive_quote',
        'auto_archive_invoice',
        'auto_email_invoice',
        'all_pages_footer',
        'all_pages_header',
        'show_currency_code',
        'enable_portal_password',
        'send_portal_password',
        'recurring_invoice_number_prefix',
        'enable_client_portal',
        'invoice_fields',
        'invoice_embed_documents',
        'document_email_attachment',
        'ubl_email_attachment',
        'enable_client_portal_dashboard',
        'page_size',
        'live_preview',
        'invoice_number_padding',
        'enable_second_tax_rate',
        'auto_bill_on_due_date',
        'start_of_week',
        'enable_buy_now_buttons',
        'include_item_taxes_inline',
        'financial_year_start',
        'enabled_modules',
        'enabled_dashboard_sections',
        'show_accept_invoice_terms',
        'show_accept_quote_terms',
        'require_invoice_signature',
        'require_quote_signature',
//      client info
        'client_number_prefix',
        'client_number_counter',
        'client_number_pattern',
//       vendor info
        'vendor_number_prefix',
        'vendor_number_counter',
        'vendor_number_pattern',
        'payment_terms',
        'reset_counter_frequency_id',
        'reset_bill_counter_frequency_id',
        'reset_counter_date',
        'reset_bill_counter_date',
        'payment_type_id',
        'gateway_fee_enabled',
        'send_item_details',
        'domain_id',
        'analytics_key',
//       sales credit info
        'credit_number_counter',
        'credit_number_prefix',
        'credit_number_pattern',
//       bill credit info
        'vendor_credit_number_counter',
        'vendor_credit_number_prefix',
        'vendor_credit_number_pattern',
        'task_rate',
        'inclusive_taxes',
        'convert_products',
        'signature_on_pdf',
        'custom_fields',
        'custom_value1',
        'custom_value2',
        'custom_messages',
    ];


    public static $basicSettings = [
        ACCOUNT_COMPANY_DETAILS,
        ACCOUNT_LOCALIZATION,
        ACCOUNT_PAYMENTS,
        ACCOUNT_TAX_RATES,
        ACCOUNT_PRODUCTS,
        ACCOUNT_NOTIFICATIONS,
        ACCOUNT_IMPORT_EXPORT,
        ACCOUNT_MANAGEMENT,
        ACCOUNT_USER_DETAILS,
    ];


    public static $advancedSettings = [
        ACCOUNT_INVOICE_SETTINGS,
        ACCOUNT_INVOICE_DESIGN,
        ACCOUNT_CLIENT_PORTAL,
        ACCOUNT_EMAIL_SETTINGS,
        ACCOUNT_TEMPLATES_AND_REMINDERS,
        ACCOUNT_BANKS,
        ACCOUNT_REPORTS,
        ACCOUNT_DATA_VISUALIZATIONS,
        ACCOUNT_API_TOKENS,
        ACCOUNT_USER_MANAGEMENT,
    ];

    public static $modules = [
        ENTITY_RECURRING_INVOICE => 1,
        ENTITY_CREDIT => 2,
        ENTITY_QUOTE => 4,
        ENTITY_TASK => 8,
        ENTITY_EXPENSE => 16,
        ENTITY_PRODUCT => 18,
        ENTITY_PERMISSION_GROUP => 22,
        ENTITY_USER => 26,
        ENTITY_WAREHOUSE => 28,
        ENTITY_ITEM_STORE => 28,
    ];

    public static $dashboardSections = [
        'total_revenue' => 1,
        'average_invoice' => 2,
        'outstanding' => 4,
    ];

    public static $customFields = [
        'client1',
        'client2',
        'vendor1',
        'vendor2',
        'contact1',
        'contact2',
        'vendor_contact1',
        'vendor_contact2',
        'product1',
        'product2',
        'invoice1',
        'invoice2',
        'BILL1',
        'BILL2',
        'invoice_surcharge1',
        'invoice_surcharge2',
        'task1',
        'task2',
        'project1',
        'project2',
        'expense1',
        'expense2',
    ];

    public static $customLabels = [
        'address1',
        'address2',
        'amount',
        'amount_paid',
        'balance',
        'balance_due',
        'blank',
        'city_state_postal',
        'client_name',
        'vendor_name',
        'company_name',
        'contact_name',
        'vendor_contact_name',
        'country',
        'credit_card',
        'credit_date',
        'credit_issued_to',
        'credit_note',
        'credit_number',
        'credit_to',
        'custom_value1',
        'custom_value2',
        'date',
        'delivery_note',
        'description',
        'details',
        'discount',
        'due_date',
        'email',
        'from',
        'gateway_fee_description',
        'gateway_fee_discount_description',
        'gateway_fee_item',
        'hours',
        'id_number',
        'invoice',
        'BILL',
        'invoice_date',
        'invoice_due_date',
        'invoice_issued_to',
        'invoice_no',
        'invoice_number',
        'invoice_to',
        'invoice_total',
        'item',
        'line_total',
        'method',
        'outstanding',
        'paid_to_date',
        'partial_due',
        'payment_date',
        'phone',
        'po_number',
        'postal_city_state',
        'product_key',
        'quantity',
        'quote',
        'quote_date',
        'quote_due_date',
        'quote_issued_to',
        'quote_no',
        'quote_number',
        'quote_to',
        'rate',
        'reference',
        'service',
        'statement',
        'statement_date',
        'statement_issued_to',
        'statement_to',
        'subtotal',
        'surcharge',
        'tax',
        'tax_invoice',
        'tax_quote',
        'taxes',
        'terms',
        'to',
        'total',
        'unit_cost',
        'valid_until',
        'vat_number',
        'website',
        'work_phone',
        'your_credit',
        'your_invoice',
        'your_quote',
        'your_statement',
    ];

    public static $customMessageTypes = [
        CUSTOM_MESSAGE_DASHBOARD,
        CUSTOM_MESSAGE_UNPAID_INVOICE,
        CUSTOM_MESSAGE_PAID_INVOICE,
        CUSTOM_MESSAGE_UNAPPROVED_QUOTE,
        CUSTOM_MESSAGE_APPROVED_QUOTE,
        CUSTOM_MESSAGE_UNAPPROVED_PROPOSAL,
        CUSTOM_MESSAGE_APPROVED_PROPOSAL,
    ];

    public function account_tokens()
    {
        return $this->hasMany('App\Models\AccountToken');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\Contact');
    }

//  sales invoices
    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }

// bill invoices
    public function bills()
    {
        return $this->hasMany('App\Models\Bill');
    }

    public function account_gateways()
    {
        return $this->hasMany('App\Models\AccountGateway');
    }

    public function account_gateway_settings()
    {
        return $this->hasMany('App\Models\AccountGatewaySettings');
    }

    public function account_email_settings()
    {
        return $this->hasOne('App\Models\AccountEmailSettings');
    }

    public function bank_accounts()
    {
        return $this->hasMany('App\Models\BankAccount');
    }

    public function tax_rates()
    {
        return $this->hasMany('App\Models\TaxRate');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function itemBrands()
    {
        return $this->hasMany('App\Models\ItemBrand')->withTrashed();
    }

    public function itemCategories()
    {
        return $this->hasMany('App\Models\ItemCategory')->withTrashed();
    }

    public function defaultDocuments()
    {
        return $this->hasMany('App\Models\Document')->where('is_default', true);
    }

    public function background_image()
    {
        return $this->hasOne('App\Models\Document', 'id', 'background_image_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function timezone()
    {
        return $this->belongsTo('App\Models\Timezone');
    }


    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    public function date_format()
    {
        return $this->belongsTo('App\Models\DateFormat');
    }

    public function datetime_format()
    {
        return $this->belongsTo('App\Models\DatetimeFormat');
    }

    public function size()
    {
        return $this->belongsTo('App\Models\Size');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function industry()
    {
        return $this->belongsTo('App\Models\Industry');
    }

    public function payment_type()
    {
        return $this->belongsTo('App\Models\PaymentType');
    }

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense')->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Payment')->withTrashed();
    }


    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    public function expense_categories()
    {
        return $this->hasMany('App\Models\ExpenseCategory')->withTrashed();
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project')->withTrashed();
    }


    public function custom_payment_terms()
    {
        return $this->hasMany('App\Models\PaymentTerm')->withTrashed();
    }


    public function setIndustryIdAttribute($value)
    {
        $this->attributes['industry_id'] = $value ?: null;
    }


    public function setCountryIdAttribute($value)
    {
        $this->attributes['country_id'] = $value ?: null;
    }

    public function setSizeIdAttribute($value)
    {
        $this->attributes['size_id'] = $value ?: null;
    }

    public function setCustomFieldsAttribute($data)
    {
        $fields = [];

        if (!is_array($data)) {
            $data = json_decode($data);
        }

        foreach ($data as $key => $value) {
            if ($value) {
                $fields[$key] = $value;
            }
        }

        $this->attributes['custom_fields'] = count($fields) ? json_encode($fields) : null;
    }

    public function getCustomFieldsAttribute($value)
    {
        return json_decode($value ?: '{}');
    }

    public function customLabel($field)
    {
        $labels = $this->custom_fields;

        return !empty($labels->$field) ? $labels->$field : '';
    }

    public function isGatewayConfigured($gatewayId = 0)
    {
        if (!$this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        if ($gatewayId) {
            return $this->getGatewayConfig($gatewayId) != false;
        } else {
            return $this->account_gateways->count() > 0;
        }
    }

    public function isEnglish()
    {
        return !$this->language_id || $this->language_id == DEFAULT_LANGUAGE;
    }

    public function hasInvoicePrefix()
    {
        if (!$this->invoice_number_prefix && !$this->quote_number_prefix) {
            return false;
        }

        return $this->invoice_number_prefix != $this->quote_number_prefix;
    }

    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }

        //$this->load('users');
        $user = $this->users()->first();

        return $user->getDisplayName();
    }

    public function getGatewaySettings($gatewayTypeId)
    {
        if (!$this->relationLoaded('account_gateway_settings')) {
            $this->load('account_gateway_settings');
        }

        foreach ($this->account_gateway_settings as $settings) {
            if ($settings->gateway_type_id == $gatewayTypeId) {
                return $settings;
            }
        }

        return false;
    }

    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;

        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    public function getMomentDateTimeFormat()
    {
        $format = $this->datetime_format ? $this->datetime_format->format_moment : DEFAULT_DATETIME_MOMENT_FORMAT;

        if ($this->military_time) {
            $format = str_replace('h:mm:ss a', 'H:mm:ss', $format);
        }

        return $format;
    }

    public function getMomentDateFormat()
    {
        $format = $this->getMomentDateTimeFormat();
        $format = str_replace('h:mm:ss a', '', $format);
        $format = str_replace('H:mm:ss', '', $format);

        return trim($format);
    }

    public function getTimezone()
    {
        if ($this->timezone) {
            return $this->timezone->name;
        } else {
            return 'US/Eastern';
        }
    }

    public function getDate($date = 'now')
    {
        if (!$date) {
            return null;
        } elseif (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }

        return $date;
    }

    public function getDateTime($date = 'now', $formatted = false)
    {
        $date = $this->getDate($date);
        $date->setTimeZone(new DateTimeZone($this->getTimezone()));

        return $formatted ? $date->format($this->getCustomDateTimeFormat()) : $date;
    }

    public function getCustomDateFormat()
    {
        return $this->date_format ? $this->date_format->format : DEFAULT_DATE_FORMAT;
    }

    public function getSampleLink()
    {
        $invitation = new Invitation();
        $invitation->account = $this;
        $invitation->invitation_key = '...';

        return $invitation->getLink();
    }

    public function formatMoney($amount, $client = null, $decorator = false)
    {
        if ($client && $client->currency_id) {
            $currencyId = $client->currency_id;
        } elseif ($this->currency_id) {
            $currencyId = $this->currency_id;
        } else {
            $currencyId = DEFAULT_CURRENCY;
        }

        if ($client && $client->country_id) {
            $countryId = $client->country_id;
        } elseif ($this->country_id) {
            $countryId = $this->country_id;
        } else {
            $countryId = false;
        }

        if (!$decorator) {
            $decorator = $this->show_currency_code ? CURRENCY_DECORATOR_CODE : CURRENCY_DECORATOR_SYMBOL;
        }

        return Utils::formatMoney($amount, $currencyId, $countryId, $decorator);
    }

    public function formatNumber($amount, $precision = 0)
    {
        if ($this->currency_id) {
            $currencyId = $this->currency_id;
        } else {
            $currencyId = DEFAULT_CURRENCY;
        }

        return Utils::formatNumber($amount, $currencyId, $precision);
    }

    public function getCurrencyId()
    {
        return $this->currency_id ?: DEFAULT_CURRENCY;
    }


    public function getCountryId()
    {
        return $this->country_id ?: DEFAULT_COUNTRY;
    }

    public function formatDate($date)
    {
        $date = $this->getDate($date);

        if (!$date) {
            return null;
        }

        return $date->format($this->getCustomDateFormat());
    }

    public function formatDateTime($date)
    {
        $date = $this->getDateTime($date);

        if (!$date) {
            return null;
        }

        return $date->format($this->getCustomDateTimeFormat());
    }

    public function formatTime($date)
    {
        $date = $this->getDateTime($date);

        if (!$date) {
            return null;
        }

        return $date->format($this->getCustomTimeFormat());
    }


    public function getCustomTimeFormat()
    {
        return $this->military_time ? 'H:i' : 'g:i a';
    }


    public function getCustomDateTimeFormat()
    {
        $format = $this->datetime_format ? $this->datetime_format->format : DEFAULT_DATETIME_FORMAT;

        if ($this->military_time) {
            $format = str_replace('g:i a', 'H:i', $format);
        }

        return $format;
    }

    /*
    public function defaultGatewayType()
    {
        $accountGateway = $this->account_gateways[0];
        $paymentDriver = $accountGateway->paymentDriver();

        return $paymentDriver->gatewayTypes()[0];
    }
    */


    public function getGatewayByType($type = false)
    {
        if (!$this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        /** @var AccountGateway $accountGateway */
        foreach ($this->account_gateways as $accountGateway) {
            if (!$type) {
                return $accountGateway;
            }

            $paymentDriver = $accountGateway->paymentDriver();

            if ($paymentDriver->handles($type)) {
                return $accountGateway;
            }
        }

        return false;
    }

    public function availableGatewaysIds()
    {
        if (!$this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        $gatewayTypes = [];
        $gatewayIds = [];
        $usedGatewayIds = [];

        foreach ($this->account_gateways as $accountGateway) {
            $usedGatewayIds[] = $accountGateway->gateway_id;
            $paymentDriver = $accountGateway->paymentDriver();
            $gatewayTypes = array_unique(array_merge($gatewayTypes, $paymentDriver->gatewayTypes()));
        }

        foreach (Cache::get('gateways') as $gateway) {
            $paymentDriverClass = AccountGateway::paymentDriverClass($gateway->provider);
            $paymentDriver = new $paymentDriverClass();
            $available = true;

            foreach ($gatewayTypes as $type) {
                if ($paymentDriver->handles($type)) {
                    $available = false;
                    break;
                }
            }
            if ($available) {
                $gatewayIds[] = $gateway->id;
            }
        }

        return $gatewayIds;
    }


    public function paymentDriver($invitation = false, $gatewayTypeId = false)
    {
        /** @var AccountGateway $accountGateway */
        if ($accountGateway = $this->getGatewayByType($gatewayTypeId)) {
            return $accountGateway->paymentDriver($invitation, $gatewayTypeId);
        }

        return false;
    }

    public function gatewayIds()
    {
        return $this->account_gateways()->pluck('gateway_id')->toArray();
    }


    public function hasGatewayId($gatewayId)
    {
        return in_array($gatewayId, $this->gatewayIds());
    }


    public function getGatewayConfig($gatewayId)
    {
        foreach ($this->account_gateways as $gateway) {
            if ($gateway->gateway_id == $gatewayId) {
                return $gateway;
            }
        }

        return false;
    }


    public function getPrimaryUser()
    {
        return $this->users()
            ->orderBy('id')
            ->first();
    }

    public function getToken($userId, $name)
    {
        foreach ($this->account_tokens as $token) {
            if ($token->user_id == $userId && $token->name === $name) {
                return $token->token;
            }
        }

        return null;
    }


    public function createInvoice($entityType = ENTITY_INVOICE, $clientId = null)
    {
        $invoice = Invoice::createNew();

        $invoice->is_recurring = false;
        $invoice->invoice_type_id = INVOICE_TYPE_STANDARD;
        $invoice->invoice_date = Utils::today();
        $invoice->start_date = Utils::today();
        $invoice->invoice_design_id = $this->invoice_design_id;
        $invoice->client_id = $clientId;
        $invoice->custom_taxes1 = $this->custom_invoice_taxes1;
        $invoice->custom_taxes2 = $this->custom_invoice_taxes2;

        if ($entityType === ENTITY_RECURRING_INVOICE) {
            $invoice->invoice_number = microtime(true);
            $invoice->is_recurring = true;
        } else {
            if ($entityType == ENTITY_QUOTE) {
                $invoice->invoice_type_id = INVOICE_TYPE_QUOTE;
                $invoice->invoice_design_id = $this->quote_design_id;
            }

            if ($this->hasClientNumberPattern($invoice) && !$clientId) {
                // do nothing, we don't yet know the value
            } elseif (!$invoice->invoice_number) {
                $invoice->invoice_number = $this->getNextNumber($invoice);
            }
        }

        if (!$clientId) {
            $invoice->client = Client::createNew();
            $invoice->client->public_id = 0;
        }

        return $invoice;
    }

    public function createBill($entityType = ENTITY_BILL, $vendorId = null)
    {
        $Bill = Bill::createNew();

        $Bill->is_recurring = false;
        $Bill->invoice_type_id = INVOICE_TYPE_STANDARD;
        $Bill->invoice_date = Utils::today();
        $Bill->start_date = Utils::today();
        $Bill->invoice_design_id = $this->invoice_design_id;
        $Bill->vendor_id = $vendorId;
        $Bill->custom_taxes1 = $this->custom_invoice_taxes1;
        $Bill->custom_taxes2 = $this->custom_invoice_taxes2;
//      to generate invoice number using micro time() carbon instance
        if ($entityType === ENTITY_RECURRING_BILL) {
            $Bill->invoice_number = microtime(true);
            $Bill->is_recurring = true;
        } else {
            if ($entityType == ENTITY_BILL_QUOTE) {
                $Bill->invoice_type_id = INVOICE_TYPE_QUOTE;
                $Bill->invoice_design_id = $this->quote_design_id;
            }

            if ($this->hasVendorNumberPattern($Bill) && !$vendorId) {
                // do nothing, we don't yet know the value
            } elseif (!$Bill->invoice_number) {
                $Bill->invoice_number = $this->getVendorNextNumber($Bill);
            }
        }

        if (!$vendorId) {
            $Bill->client = Vendor::createNew();
            $Bill->client->public_id = 0;
        }

        return $Bill;
    }

    public function loadLocalizationSettings($entity = false)
    {
        $this->load('timezone', 'date_format', 'datetime_format', 'language');

        $timezone = $this->timezone ? $this->timezone->name : DEFAULT_TIMEZONE;
        Session::put(SESSION_TIMEZONE, $timezone);

        Session::put(SESSION_DATE_FORMAT, $this->date_format ? $this->date_format->format : DEFAULT_DATE_FORMAT);
        Session::put(SESSION_DATE_PICKER_FORMAT, $this->date_format ? $this->date_format->picker_format : DEFAULT_DATE_PICKER_FORMAT);

        $currencyId = (($entity && $entity->currency_id) ? $entity->currency_id : $this->currency_id) ?: DEFAULT_CURRENCY;
        $locale = (($entity && $entity->language_id) ? $entity->language->locale : ($this->language_id)) ? $this->Language->locale : DEFAULT_LOCALE;
        Session::put(SESSION_CURRENCY, $currencyId);
        Session::put(SESSION_CURRENCY_DECORATOR, $this->show_currency_code ? CURRENCY_DECORATOR_CODE : CURRENCY_DECORATOR_SYMBOL);
        Session::put(SESSION_LOCALE, $locale);

        App::setLocale($locale);

        $format = $this->datetime_format ? $this->datetime_format->format : DEFAULT_DATETIME_FORMAT;
        if ($this->military_time) {
            $format = str_replace('g:i a', 'H:i', $format);
        }
        Session::put(SESSION_DATETIME_FORMAT, $format);

        Session::put('start_of_week', $this->start_of_week);
    }

    public function isNinjaAccount()
    {
//        return strpos($this->account_key, 'zg4ylmzDkdkPOT8yoKQw9LTWaoZJx7') === 0;
        return strpos($this->account_key, 'zg4ylmzDkdkPOT8yoKQw9LTWaoZJx7') === 0;
    }

    public function isNinjaOrLicenseAccount()
    {
        return $this->isNinjaAccount() || $this->account_key == NINJA_LICENSE_ACCOUNT_KEY;
    }

    public function startTrial($plan)
    {
        if (!Utils::isNinja()) {
            return;
        }

        if ($this->company->trial_started && $this->company->trial_started != '0000-00-00') {
            return;
        }

        $this->company->trial_plan = $plan;
        $this->company->trial_started = date_create()->format('Y-m-d');
        $this->company->save();
    }

    public function hasReminders()
    {
        if (!$this->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)) {
            return false;
        }

        return $this->enable_reminder1 || $this->enable_reminder2 || $this->enable_reminder3 || $this->enable_reminder4;
    }

    public function hasFeature($feature)
    {
        $planDetails = $this->getPlanDetails();
        $selfHost = !Utils::isNinjaProd();
        if (!$selfHost && function_exists('ninja_account_features')) {
            $result = ninja_account_features($this, $feature);

            if ($result != null) {
                return $result;
            }
        }

        switch ($feature) {
            // Pro
            case FEATURE_TASKS:
            case FEATURE_EXPENSES:
            case FEATURE_QUOTES:
                return true;

            case FEATURE_CUSTOMIZE_INVOICE_DESIGN:
            case FEATURE_DIFFERENT_DESIGNS:
            case FEATURE_EMAIL_TEMPLATES_REMINDERS:
            case FEATURE_INVOICE_SETTINGS:
            case FEATURE_CUSTOM_EMAILS:
            case FEATURE_PDF_ATTACHMENT:
            case FEATURE_MORE_INVOICE_DESIGNS:
            case FEATURE_REPORTS:
            case FEATURE_BUY_NOW_BUTTONS:
            case FEATURE_API:
            case FEATURE_CLIENT_PORTAL_PASSWORD:
            case FEATURE_CUSTOM_URL:
                return $selfHost || !empty($planDetails);

            // Pro; No trial allowed, unless they're trialing enterprise with an active pro plan
            case FEATURE_MORE_CLIENTS:
                return $selfHost || !empty($planDetails) && (!$planDetails['trial'] || !empty($this->getPlanDetails(false, false)));

            // White Label
            case FEATURE_WHITE_LABEL:
                if ($this->isNinjaAccount() || (!$selfHost && $planDetails && !$planDetails['expires'])) {
                    return false;
                }
            // Fallthrough
            case FEATURE_REMOVE_CREATED_BY:
                return !empty($planDetails); // A plan is required even for self-hosted users

            // Enterprise; No Trial allowed; grandfathered for old pro users
            case FEATURE_USERS:// Grandfathered for old Pro users
                if ($planDetails && $planDetails['trial']) {
                    // Do they have a non-trial plan?
                    $planDetails = $this->getPlanDetails(false, false);
                }

                return $selfHost || !empty($planDetails) && ($planDetails['plan'] == PLAN_ENTERPRISE || $planDetails['started'] <= date_create(PRO_USERS_GRANDFATHER_DEADLINE));

            // Enterprise; No Trial allowed
            case FEATURE_DOCUMENTS:
            case FEATURE_USER_PERMISSIONS:
                return $selfHost || !empty($planDetails) && $planDetails['plan'] == PLAN_ENTERPRISE && !$planDetails['trial'];

            default:
                return false;
        }
    }

    public function isPaid()
    {
        return Utils::isNinja() ? ($this->isPro() && !$this->isTrial()) : Utils::isWhiteLabel();
    }

    public function isPro(&$plan_details = null)
    {
        if (!Utils::isNinjaProd()) {
            return true;
        }

        if ($this->isNinjaAccount()) {
            return true;
        }

        $plan_details = $this->getPlanDetails();

        return !empty($plan_details);
    }

    public function hasActivePromo()
    {
        return $this->company->hasActivePromo();
    }

    public function isEnterprise(&$plan_details = null)
    {
        if (!Utils::isNinjaProd()) {
            return true;
        }

        if ($this->isNinjaAccount()) {
            return true;
        }

        $plan_details = $this->getPlanDetails();

        return $plan_details && $plan_details['plan'] == PLAN_ENTERPRISE;
    }

    public function getPlanDetails($include_inactive = false, $include_trial = true)
    {
        if (!$this->company) {
            return null;
        }

        $plan = $this->company->plan;
        $price = $this->company->plan_price;
        $trial_plan = $this->company->trial_plan;

        if ((!$plan || $plan == PLAN_FREE) && (!$trial_plan || !$include_trial)) {
            return null;
        }
        $trial_active = false;
        if ($trial_plan && $include_trial) {
            $trial_started = DateTime::createFromFormat('Y-m-d', $this->company->trial_started);
            $trial_expires = clone $trial_started;
            $trial_expires->modify('+2 weeks');

            if ($trial_expires >= date_create()) {
                $trial_active = true;
            }
        }

        $plan_active = false;
        if ($plan) {
            if ($this->company->plan_expires == null) {
                $plan_active = true;
                $plan_expires = false;
            } else {
                $plan_expires = DateTime::createFromFormat('Y-m-d', $this->company->plan_expires);
                if ($plan_expires >= date_create()) {
                    $plan_active = true;
                }
            }
        }

        if (!$include_inactive && !$plan_active && !$trial_active) {
            return null;
        }

        // Should we show plan details or trial details?
        if (($plan && !$trial_plan) || !$include_trial) {
            $use_plan = true;
        } elseif (!$plan && $trial_plan) {
            $use_plan = false;
        } else {
            // There is both a plan and a trial
            if (!empty($plan_active) && empty($trial_active)) {
                $use_plan = true;
            } elseif (empty($plan_active) && !empty($trial_active)) {
                $use_plan = false;
            } elseif (!empty($plan_active) && !empty($trial_active)) {
                // Both are active; use whichever is a better plan
                if ($plan == PLAN_ENTERPRISE) {
                    $use_plan = true;
                } elseif ($trial_plan == PLAN_ENTERPRISE) {
                    $use_plan = false;
                } else {
                    // They're both the same; show the plan
                    $use_plan = true;
                }
            } else {
                // Neither are active; use whichever expired most recently
                $use_plan = $plan_expires >= $trial_expires;
            }
        }

        if ($use_plan) {
            return [
                'company_id' => $this->company->id,
                'num_users' => $this->company->num_users,
                'plan_price' => $price,
                'trial' => false,
                'plan' => $plan,
                'started' => DateTime::createFromFormat('Y-m-d', $this->company->plan_started),
                'expires' => $plan_expires,
                'paid' => DateTime::createFromFormat('Y-m-d', $this->company->plan_paid),
                'term' => $this->company->plan_term,
                'active' => $plan_active,
            ];
        } else {
            return [
                'company_id' => $this->company->id,
                'num_users' => 1,
                'plan_price' => 0,
                'trial' => true,
                'plan' => $trial_plan,
                'started' => $trial_started,
                'expires' => $trial_expires,
                'active' => $trial_active,
            ];
        }
    }

    public function isTrial()
    {
        if (!Utils::isNinjaProd()) {
            return false;
        }

        $plan_details = $this->getPlanDetails();

        return $plan_details && $plan_details['trial'];
    }

    public function getCountTrialDaysLeft()
    {
        $planDetails = $this->getPlanDetails(true);

        if (!$planDetails || !$planDetails['trial']) {
            return 0;
        }

        $today = new DateTime('now');
        $interval = $today->diff($planDetails['expires']);

        return $interval ? $interval->d : 0;
    }

    public function getRenewalDate()
    {
        $planDetails = $this->getPlanDetails();

        if ($planDetails) {
            $date = $planDetails['expires'];
            $date = max($date, date_create());
        } else {
            $date = date_create();
        }

        return Carbon::instance($date);
    }

    public function getSubscriptions($eventId)
    {
        return Subscription::where('account_id', $this->id)->where('event_id', $eventId)->get();
    }

    /**
     * @return $this
     */
    public function hideFieldsForViz()
    {
        foreach ($this->clients as $client) {
            $client->setVisible([
                'public_id',
                'name',
                'balance',
                'paid_to_date',
                'invoices',
                'contacts',
                'currency_id',
                'currency',
            ]);

            foreach ($client->invoices as $invoice) {
                $invoice->setVisible([
                    'public_id',
                    'invoice_number',
                    'amount',
                    'balance',
                    'invoice_status_id',
                    'invoice_items',
                    'created_at',
                    'is_recurring',
                    'invoice_type_id',
                    'is_public',
                    'due_date',
                ]);

                foreach ($invoice->invoice_items as $invoiceItem) {
                    $invoiceItem->setVisible([
                        'product_key',
                        'cost',
                        'qty',
                        'discount',
                    ]);
                }
            }

            foreach ($client->contacts as $contact) {
                $contact->setVisible([
                    'public_id',
                    'first_name',
                    'last_name',
                    'email',]);
            }
        }

        return $this;
    }

    public function showTokenCheckbox(&$storage_gateway = null)
    {
        if (!($storage_gateway = $this->getTokenGatewayId())) {
            return false;
        }

        return $this->token_billing_type_id == TOKEN_BILLING_OPT_IN
            || $this->token_billing_type_id == TOKEN_BILLING_OPT_OUT;
    }

    public function getTokenGatewayId()
    {
        if ($this->isGatewayConfigured(GATEWAY_STRIPE)) {
            return GATEWAY_STRIPE;
        } elseif ($this->isGatewayConfigured(GATEWAY_BRAINTREE)) {
            return GATEWAY_BRAINTREE;
        } elseif ($this->isGatewayConfigured(GATEWAY_WEPAY)) {
            return GATEWAY_WEPAY;
        } else {
            return false;
        }
    }

    public function getTokenGateway()
    {
        $gatewayId = $this->getTokenGatewayId();
        if (!$gatewayId) {
            return;
        }

        return $this->getGatewayConfig($gatewayId);
    }

    public function getLocale()
    {
        return $this->language_id && $this->language ? $this->language->locale : DEFAULT_LOCALE;
    }

    public function selectTokenCheckbox()
    {
        return $this->token_billing_type_id == TOKEN_BILLING_OPT_OUT;
    }

    public function getSiteUrl()
    {
        $url = trim(SITE_URL, '/');
        $iframe_url = $this->iframe_url;

        if ($iframe_url) {
            return "{$iframe_url}/?";
        } elseif ($this->subdomain) {
            $url = Utils::replaceSubdomain($url, $this->subdomain);
        }

        return $url;
    }

    public function checkSubdomain($host)
    {
        if (!$this->subdomain) {
            return true;
        }

        $server = explode('.', $host);
        $subdomain = $server[0];

        if (!in_array($subdomain, ['app', 'www']) && $subdomain != $this->subdomain) {
            return false;
        }

        return true;
    }

    public function attachPDF()
    {
        return $this->hasFeature(FEATURE_PDF_ATTACHMENT) && $this->pdf_email_attachment;
    }

    public function attachUBL()
    {
        return $this->hasFeature(FEATURE_PDF_ATTACHMENT) && $this->ubl_email_attachment;
    }

    public function getEmailDesignId()
    {
        return $this->hasFeature(FEATURE_CUSTOM_EMAILS) ? $this->email_design_id : EMAIL_DESIGN_PLAIN;
    }

    public function clientViewCSS()
    {
        $css = '';

        if ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)) {
            $bodyFont = $this->getBodyFontCss();
            $headerFont = $this->getHeaderFontCss();

            $css = 'body{' . $bodyFont . '}';
            if ($headerFont != $bodyFont) {
                $css .= 'h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6{' . $headerFont . '}';
            }

            $css .= $this->client_view_css;
        }

        return $css;
    }

    public function getFontsUrl($protocol = '')
    {
        $bodyFont = $this->getHeaderFontId();
        $headerFont = $this->getBodyFontId();

        $bodyFontSettings = Utils::getFromCache($bodyFont, 'fonts');
        $google_fonts = [$bodyFontSettings['google_font']];

        if ($headerFont != $bodyFont) {
            $headerFontSettings = Utils::getFromCache($headerFont, 'fonts');
            $google_fonts[] = $headerFontSettings['google_font'];
        }

        return ($protocol ? $protocol . ':' : '') . '//fonts.googleapis.com/css?family=' . implode('|', $google_fonts);
    }

    public function getHeaderFontId()
    {
        return ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) && $this->header_font_id) ? $this->header_font_id : DEFAULT_HEADER_FONT;
    }

    public function getBodyFontId()
    {
        return ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) && $this->body_font_id) ? $this->body_font_id : DEFAULT_BODY_FONT;
    }

    public function getHeaderFontName()
    {
        return Utils::getFromCache($this->getHeaderFontId(), 'fonts')['name'];
    }

    public function getBodyFontName()
    {
        return Utils::getFromCache($this->getBodyFontId(), 'fonts')['name'];
    }

    public function getHeaderFontCss($include_weight = true)
    {
        $font_data = Utils::getFromCache($this->getHeaderFontId(), 'fonts');
        $css = 'font-family:' . $font_data['css_stack'] . ';';

        if ($include_weight) {
            $css .= 'font-weight:' . $font_data['css_weight'] . ';';
        }

        return $css;
    }

    public function getBodyFontCss($include_weight = true)
    {
        $font_data = Utils::getFromCache($this->getBodyFontId(), 'fonts');
        $css = 'font-family:' . $font_data['css_stack'] . ';';

        if ($include_weight) {
            $css .= 'font-weight:' . $font_data['css_weight'] . ';';
        }

        return $css;
    }

    public function getFonts()
    {
        return array_unique([$this->getHeaderFontId(), $this->getBodyFontId()]);
    }

    public function getFontsData()
    {
        $data = [];

        foreach ($this->getFonts() as $font) {
            $data[] = Utils::getFromCache($font, 'fonts');
        }

        return $data;
    }

    public function getFontFolders()
    {
        return array_map(function ($item) {
            return $item['folder'];
        }, $this->getFontsData());
    }

    public function isModuleEnabled($entityType)
    {
        if (!in_array($entityType, [
            ENTITY_RECURRING_INVOICE,
            ENTITY_CREDIT,
            ENTITY_QUOTE,
            ENTITY_TASK,
            ENTITY_EXPENSE,
            ENTITY_VENDOR,
            ENTITY_PROJECT,
            ENTITY_PROPOSAL,
            ENTITY_PRODUCT,
            ENTITY_USER,
            ENTITY_PERMISSION,
            ENTITY_PERMISSION_GROUP,
            ENTITY_WAREHOUSE,
            ENTITY_BRANCH,
            ENTITY_ITEM_STORE,
            ENTITY_ITEM_TRANSFER,
            ENTITY_ITEM_MOVEMENT,
            ENTITY_LOCATION,
            ENTITY_ITEM_PRICE,
            ENTITY_ITEM_CATEGORY,
            ENTITY_ITEM_BRAND,
        ])) {
            return true;
        }

        if ($entityType == ENTITY_VENDOR) {
            $entityType = ENTITY_EXPENSE;
        } elseif ($entityType == ENTITY_PROJECT) {
            $entityType = ENTITY_TASK;
        } elseif ($entityType == ENTITY_PROPOSAL) {
            $entityType = ENTITY_QUOTE;
        }
        // note: single & checks bitmask match
//        return $this->enabled_modules & static::$modules[$entityType];
        return $this->enabled_modules;
    }

    public function requiresAuthorization($invoice)
    {
        return $this->showAcceptTerms($invoice) || $this->showSignature($invoice);
    }

    public function showAcceptTerms($invoice)
    {
        if (!$this->isPro()) {
            return false;
        }

        return $invoice->isQuote() ? $this->show_accept_quote_terms : $this->show_accept_invoice_terms;
    }

    public function showSignature($invoice)
    {
        if (!$this->isPro()) {
            return false;
        }

        return $invoice->isQuote() ? $this->require_quote_signature : $this->require_invoice_signature;
    }

    public function emailMarkupEnabled()
    {
        if (!Utils::isNinja()) {
            return false;
        }

        return $this->enable_email_markup;
    }

    public function defaultDaysDue($client = false)
    {
        if ($client && $client->payment_terms != 0) {
            return $client->defaultDaysDue();
        }

        return $this->payment_terms == -1 ? 0 : $this->payment_terms;
    }

    public function defaultDueDate($client = false)
    {
        if ($client && $client->payment_terms != 0) {
            $numDays = $client->defaultDaysDue();
        } elseif ($this->payment_terms != 0) {
            $numDays = $this->defaultDaysDue();
        } else {
            return null;
        }

        return Carbon::now()->addDays($numDays)->format('Y-m-d');
    }

    public function hasMultipleAccounts()
    {
        return $this->company->accounts->count() > 1;
    }

    public function hasMultipleUsers()
    {
        return $this->users->count() > 1;
    }

    public function getPrimaryAccount()
    {
        return $this->company->accounts()->orderBy('id')->first();
    }

    public function financialYearStartMonth()
    {
        if (!$this->financial_year_start) {
            return 1;
        }

        $yearStart = Carbon::parse($this->financial_year_start);

        return $yearStart ? $yearStart->month : 1;
    }

    public function financialYearStart()
    {
        if (!$this->financial_year_start) {
            return false;
        }

        $yearStart = Carbon::parse($this->financial_year_start);
        $yearStart->year = date('Y');

        if ($yearStart->isFuture()) {
            $yearStart->subYear();
        }

        return $yearStart->format('Y-m-d');
    }

    public function isClientPortalPasswordEnabled()
    {
        return $this->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD) && $this->enable_portal_password;
    }

    public function isVendorPortalPasswordEnabled()
    {
        return $this->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD) && $this->enable_portal_password;
    }

    public function getBaseUrl()
    {
        if ($this->hasFeature(FEATURE_CUSTOM_URL)) {
            if ($this->iframe_url) {
                return $this->iframe_url;
            }

            if (Utils::isNinjaProd() && !Utils::isReseller()) {
                $url = $this->present()->clientPortalLink();
            } else {
                $url = url('/');
            }

            if ($this->subdomain) {
                $url = Utils::replaceSubdomain($url, $this->subdomain);
            }

            return $url;
        } else {
            return url('/');
        }
    }

    public function requiresAddressState()
    {
        return true;
        //return ! $this->country_id || $this->country_id == DEFAULT_COUNTRY;
    }
}

Account::creating(function ($account) {
    LookupAccount::createAccount($account->account_key, $account->company_id);
});

Account::updating(function ($account) {
    $dirty = $account->getDirty();
    if (array_key_exists('subdomain', $dirty)) {
        LookupAccount::updateAccount($account->account_key, $account);
    }
});

Account::updated(function ($account) {
    // prevent firing event if the invoice/quote counter was changed
    // TODO: remove once counters are moved to separate table
    $dirty = $account->getDirty();
    if (isset($dirty['invoice_number_counter']) || isset($dirty['quote_number_counter'])) {
        return;
    }

    Event::fire(new UserSettingsChanged());
});

Account::deleted(function ($account) {
    LookupAccount::deleteWhere([
        'account_key' => $account->account_key
    ]);
});
