<?php

namespace App\Models\Common;

use App;
use App\Events\Auth\UserSettingsChangedEvent;
use App\Libraries\Utils;
use App\Models\Bill;
use App\Models\Client;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\LookupAccount;
use App\Models\Traits\GenerateInvoiceNumbers;
use App\Models\Traits\GenerateBillNumbers;
use App\Models\Traits\HasCustomMessages;
use App\Models\Traits\HasLogo;
use App\Models\Traits\PresentsInvoice;
use App\Models\Traits\SendsEmails;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Laracasts\Presenter\PresentableTrait;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

/**
 * Class Account Model.
 *
 * @property int $id
 * @property int|null $timezone_id
 * @property int|null $date_format_id
 * @property int|null $datetime_format_id
 * @property int|null $currency_id
 * @property int|null $country_id
 * @property int|null $industry_id
 * @property int|null $size_id
 * @property int|null $company_id
 * @property int|null $invoice_design_id
 * @property int|null $language_id
 * @property int|null $payment_type_id
 * @property string|null $account_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $name
 * @property string|null $ip
 * @property string|null $last_login
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $invoice_terms
 * @property string|null $bill_terms
 * @property string|null $email_footer
 * @property int $invoice_taxes
 * @property int $invoice_item_taxes
 * @property int $bill_taxes
 * @property int $bill_item_taxes
 * @property string|null $work_phone
 * @property string|null $work_email
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property int $fill_products
 * @property int $update_products
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property int $hide_quantity
 * @property int $hide_paid_to_date
 * @property int|null $custom_invoice_taxes1
 * @property int|null $custom_invoice_taxes2
 * @property int|null $custom_bill_taxes1
 * @property int|null $custom_bill_taxes2
 * @property string|null $vat_number
 * @property string|null $invoice_number_prefix
 * @property int|null $invoice_number_counter
 * @property string|null $invoice_number_pattern
 * @property string|null $quote_number_prefix
 * @property int|null $quote_number_counter
 * @property string|null $quote_number_pattern
 * @property string|null $recurring_invoice_number_prefix
 * @property int $share_counter
 * @property string|null $bill_quote_number_prefix
 * @property int|null $bill_quote_number_counter
 * @property string|null $bill_quote_number_pattern
 * @property int $share_bill_counter
 * @property string|null $id_number
 * @property int $token_billing_type_id
 * @property string|null $invoice_footer
 * @property string|null $bill_footer
 * @property int $pdf_email_attachment
 * @property string|null $subdomain
 * @property int $font_size
 * @property string|null $invoice_labels
 * @property string|null $invoice_fields
 * @property string|null $bill_fields
 * @property string|null $bill_labels
 * @property string|null $custom_design1
 * @property int $show_item_taxes
 * @property string|null $iframe_url
 * @property int $military_time
 * @property int $enable_reminder1
 * @property int $enable_reminder2
 * @property int $enable_reminder3
 * @property int $num_days_reminder1
 * @property int $num_days_reminder2
 * @property int $num_days_reminder3
 * @property int $recurring_hour
 * @property string|null $quote_terms
 * @property string|null $bill_quote_terms
 * @property int $email_design_id
 * @property int $enable_email_markup
 * @property string|null $website
 * @property int $direction_reminder1
 * @property int $direction_reminder2
 * @property int $direction_reminder3
 * @property int $field_reminder1
 * @property int $field_reminder2
 * @property int $field_reminder3
 * @property string|null $client_view_css
 * @property int $header_font_id
 * @property int $body_font_id
 * @property int $auto_convert_quote
 * @property int $auto_convert_bill_quote
 * @property int $all_pages_footer
 * @property int $all_pages_header
 * @property int $show_currency_code
 * @property int $enable_portal_password
 * @property int $send_portal_password
 * @property int $enable_client_portal
 * @property int $enable_vendor_portal
 * @property string|null $devices
 * @property string|null $logo
 * @property int $logo_width
 * @property int $logo_height
 * @property int $logo_size
 * @property int $invoice_embed_documents
 * @property int $bill_embed_documents
 * @property int $document_email_attachment
 * @property int $enable_client_portal_dashboard
 * @property string|null $page_size
 * @property int $live_preview
 * @property int $invoice_number_padding
 * @property int $enable_second_tax_rate
 * @property int $auto_invoice_on_due_date
 * @property int $auto_bill_on_due_date
 * @property int $start_of_week
 * @property int $enable_buy_now_buttons
 * @property int $include_item_taxes_inline
 * @property int $include_items_discount_inline
 * @property string|null $financial_year_start
 * @property int $enabled_modules
 * @property int $enabled_dashboard_sections
 * @property int $show_accept_invoice_terms
 * @property int $show_accept_quote_terms
 * @property int $require_invoice_signature
 * @property int $require_quote_signature
 * @property int $require_bill_signature
 * @property int $require_bill_quote_signature
 * @property string|null $client_number_prefix
 * @property int|null $client_number_counter
 * @property string|null $client_number_pattern
 * @property string|null $vendor_number_prefix
 * @property int|null $vendor_number_counter
 * @property string|null $vendor_number_pattern
 * @property int|null $domain_id
 * @property int|null $payment_terms
 * @property int|null $reset_counter_frequency_id
 * @property string|null $reset_counter_date
 * @property int|null $reset_bill_counter_frequency_id
 * @property string|null $reset_bill_counter_date
 * @property int $gateway_fee_enabled
 * @property string|null $tax_name1
 * @property float $tax_rate1
 * @property string|null $tax_name2
 * @property float $tax_rate2
 * @property int $quote_design_id
 * @property string|null $custom_design2
 * @property string|null $custom_design3
 * @property string|null $analytics_key
 * @property int|null $credit_number_counter
 * @property string|null $credit_number_prefix
 * @property string|null $credit_number_pattern
 * @property int|null $vendor_credit_number_counter
 * @property string|null $vendor_credit_number_prefix
 * @property string|null $vendor_credit_number_pattern
 * @property float $task_rate
 * @property int $inclusive_taxes
 * @property int $convert_products
 * @property int $enable_reminder4
 * @property int $signature_on_pdf
 * @property int $ubl_email_attachment
 * @property int|null $auto_archive_invoice
 * @property int|null $auto_archive_quote
 * @property int|null $auto_email_invoice
 * @property int|null $auto_archive_bill
 * @property int|null $auto_archive_bill_quote
 * @property int|null $auto_email_bill
 * @property int|null $send_item_details
 * @property string|null $custom_fields
 * @property int|null $background_image_id
 * @property string|null $custom_messages
 * @property int $is_custom_domain
 * @property int $show_product_notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read AccountEmailSettings|null $account_email_settings
 * @property-read Collection|AccountGatewaySettings[] $account_gateway_settings
 * @property-read int|null $account_gateway_settings_count
 * @property-read Collection|AccountGateway[] $account_gateways
 * @property-read int|null $account_gateways_count
 * @property-read Collection|AccountToken[] $account_tokens
 * @property-read int|null $account_tokens_count
 * @property-read Document|null $background_image
 * @property-read Collection|BankAccount[] $bank_accounts
 * @property-read int|null $bank_accounts_count
 * @property-read Collection|Bill[] $bills
 * @property-read int|null $bills_count
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read Company|null $company
 * @property-read Collection|Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read Country|null $country
 * @property-read Currency|null $currency
 * @property-read Collection|PaymentTerm[] $custom_payment_terms
 * @property-read int|null $custom_payment_terms_count
 * @property-read DateFormat|null $date_format
 * @property-read DatetimeFormat|null $datetime_format
 * @property-read Collection|Document[] $defaultDocuments
 * @property-read int|null $default_documents_count
 * @property-read Collection|ExpenseCategory[] $expense_categories
 * @property-read int|null $expense_categories_count
 * @property-read Collection|Expense[] $expenses
 * @property-read int|null $expenses_count
 * @property-read Industry|null $industry
 * @property-read Collection|Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read Collection|ItemBrand[] $itemBrands
 * @property-read int|null $item_brands_count
 * @property-read Collection|ItemCategory[] $itemCategories
 * @property-read int|null $item_categories_count
 * @property-read Language|null $language
 * @property-read PaymentType|null $payment_type
 * @property-read Collection|Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read Collection|Project[] $projects
 * @property-read int|null $projects_count
 * @property-read Size|null $size
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection|TaxRate[] $tax_rates
 * @property-read int|null $tax_rates_count
 * @property-read Timezone|null $timezone
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static Builder|Account onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccountKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAllPagesFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAllPagesHeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAnalyticsKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoArchiveBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoArchiveBillQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoArchiveInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoArchiveQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoBillOnDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoConvertBillQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoConvertQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoEmailBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoEmailInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoInvoiceOnDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBackgroundImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillEmbedDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillItemTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillNumberPadding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillQuoteNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillQuoteNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillQuoteNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillQuoteTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBillTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBodyFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereClientNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereClientNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereClientNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereClientViewCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereConvertProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreditNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreditNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreditNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomBillTaxes1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomBillTaxes2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomDesign1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomDesign2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomDesign3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomInvoiceTaxes1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomInvoiceTaxes2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDateFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDatetimeFormatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDevices($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDirectionReminder1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDirectionReminder2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDirectionReminder3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDocumentEmailAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEmailDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEmailFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableBuyNowButtons($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableClientPortal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableClientPortalDashboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableEmailMarkup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnablePortalPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableReminder1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableReminder2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableReminder3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableReminder4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableSecondTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnableVendorPortal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnabledDashboardSections($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEnabledModules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereFieldReminder1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereFieldReminder2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereFieldReminder3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereFillProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereFinancialYearStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereGatewayFeeEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereHeaderFontId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereHidePaidToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereHideQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIframeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIncludeItemTaxesInline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIncludeItemsDiscountInline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInclusiveTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceEmbedDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceItemTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceNumberPadding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereInvoiceTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIsCustomDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLivePreview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLogoHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLogoSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLogoWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereMilitaryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereNumDaysReminder1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereNumDaysReminder2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereNumDaysReminder3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePageSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePdfEmailAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereQuoteDesignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereQuoteNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereQuoteNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereQuoteNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereQuoteTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRecurringBillNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRecurringHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRecurringInvoiceNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRequireBillQuoteSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRequireBillSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRequireInvoiceSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRequireQuoteSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereResetBillCounterDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereResetBillCounterFrequencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereResetCounterDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereResetCounterFrequencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSendItemDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSendPortalPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShareBillCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShareCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShowAcceptInvoiceTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShowAcceptQuoteTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShowCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShowItemTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShowProductNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSignatureOnPdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereStartOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSubdomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTaskRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTimezoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTokenBillingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUblEmailAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdateProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereVendorCreditNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereVendorCreditNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereVendorCreditNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereVendorNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereVendorNumberPattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereVendorNumberPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereWorkEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereWorkPhone($value)
 * @method static Builder|Account withTrashed()
 * @method static Builder|Account withoutTrashed()
 * @mixin \Eloquent
 */
class Account extends Eloquent
{
    use PresentableTrait;
    use SoftDeletes;
    use PresentsInvoice;
    use GenerateInvoiceNumbers;
    use GenerateBillNumbers;
    use SendsEmails;
    use HasLogo;
    use HasCustomMessages;


    protected $presenter = 'App\Ninja\Presenters\AccountPresenter';


    protected $dates = ['created_at', 'updated_at'];

    protected $hidden = ['ip', 'deleted_at'];


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
        'bill_terms',
        'email_footer',
        'industry_id',
        'size_id',
        'invoice_taxes',
        'invoice_item_taxes',
        'bill_taxes',
        'bill_item_taxes',
        'invoice_design_id',
        'quote_design_id',
        'invoice_design_id',
        'bill_quote_design_id',
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
        'invoice_number_prefix',
        'invoice_number_counter',
        'quote_number_prefix',
        'quote_number_counter',
        'share_counter',
        'bill_number_prefix',
        'bill_number_counter',
        'bill_quote_number_prefix',
        'bill_quote_number_counter',
        'share_bill_counter',
        'id_number',
        'token_billing_type_id',
        'invoice_footer',
        'bill_footer',
        'pdf_email_attachment',
        'font_size',
        'invoice_labels',
        'bill_labels',
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
        'invoice_number_pattern',
        'bill_quote_number_pattern',
        'bill_quote_terms',
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
        'recurring_invoice_number_prefix',
        'enable_client_portal',
        'invoice_fields',
        'bill_fields',
        'invoice_embed_documents',
        'document_email_attachment',
        'ubl_email_attachment',
        'enable_client_portal_dashboard',
        'page_size',
        'live_preview',
        'invoice_number_padding',
        'invoice_number_padding',
        'enable_second_tax_rate',
        'auto_invoice_on_due_date',
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
        'require_bill_signature',
        'require_bill_quote_signature',
        'client_number_prefix',
        'client_number_counter',
        'client_number_pattern',
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
        'credit_number_counter',
        'credit_number_prefix',
        'credit_number_pattern',
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
        'bill1',
        'bill2',
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
        'bill',
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
        return $this->hasMany('App\Models\Common\AccountToken');
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

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice');
    }

    public function bills()
    {
        return $this->hasMany('App\Models\Bill');
    }

    public function account_gateways()
    {
        return $this->hasMany('App\Models\Common\AccountGateway');
    }

    public function account_gateway_settings()
    {
        return $this->hasMany('App\Models\Common\AccountGatewaySettings');
    }

    public function account_email_settings()
    {
        return $this->hasOne('App\Models\Common\AccountEmailSettings');
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

    public function subscriptions()
    {
        return $this->hasMany('App\Models\Common\Subscription');
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
                $invoice->invoice_type_id = BILL_TYPE_QUOTE;
                $invoice->invoice_design_id = $this->quote_design_id;
            }

            if ($this->hasClientNumberPattern($invoice) && !$clientId) {
                // do nothing, we don't yet know the value
            } elseif (!$invoice->invoice_number) {
                $invoice->invoice_number = $this->getInvoiceNextNumber($invoice);
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
        $bill = Bill::createNew();

        $bill->is_recurring = false;
        $bill->bill_type_id = BILL_TYPE_STANDARD;
        $bill->bill_date = Utils::today();
        $bill->start_date = Utils::today();
        $bill->invoice_design_id = $this->invoice_design_id;
        $bill->vendor_id = $vendorId;
        $bill->custom_taxes1 = $this->custom_bill_taxes1;
        $bill->custom_taxes2 = $this->custom_bill_taxes2;
//      to generate bill number using micro time() carbon instance
        if ($entityType === ENTITY_RECURRING_BILL) {
            $bill->invoice_number = microtime(true);
            $bill->is_recurring = true;
        } else {
            if ($entityType == ENTITY_BILL_QUOTE) {
                $bill->bill_type_id = BILL_TYPE_QUOTE;
                $bill->invoice_design_id = $this->quote_design_id;
            }

            if (isset($bill) && $this->hasVendorNumberPattern($bill) && !$vendorId) {
                // do nothing, we don't yet know the value
            } elseif (!$bill->invoice_number) {
                $bill->invoice_number = $this->getBillNextNumber($bill);
            }
        }

        if (!$vendorId) {
            $bill->client = Vendor::createNew();
            $bill->client->public_id = 0;
        }

        return $bill;
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

    public function defaultVendorDueDate($vendor = false)
    {
        if ($vendor && $vendor->payment_terms != 0) {
            $numDays = $vendor->defaultDaysDue();
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
        return $this->hasFeature(FEATURE_vendor_PORTAL_PASSWORD) && $this->enable_portal_password;
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

    Event::fire(new UserSettingsChangedEvent());
});

Account::deleted(function ($account) {
    LookupAccount::deleteWhere([
        'account_key' => $account->account_key
    ]);
});
