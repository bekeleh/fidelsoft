<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{

    use Eloquent;

    /**
 * Class Account.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $reply_to_email
 * @property string|null $bcc_email
 * @property string|null $email_subject_invoice
 * @property string|null $email_subject_quote
 * @property string|null $email_subject_payment
 * @property string|null $email_template_invoice
 * @property string|null $email_template_quote
 * @property string|null $email_template_payment
 * @property string|null $email_subject_reminder1
 * @property string|null $email_subject_reminder2
 * @property string|null $email_subject_reminder3
 * @property string|null $email_template_reminder1
 * @property string|null $email_template_reminder2
 * @property string|null $email_template_reminder3
 * @property float|null $late_fee1_amount
 * @property float|null $late_fee1_percent
 * @property float|null $late_fee2_amount
 * @property float|null $late_fee2_percent
 * @property float|null $late_fee3_amount
 * @property float|null $late_fee3_percent
 * @property string|null $email_subject_reminder4
 * @property string|null $email_template_reminder4
 * @property int|null $frequency_id_reminder4
 * @property string|null $email_subject_proposal
 * @property string|null $email_template_proposal
 * @method static Builder|AccountEmailSettings newModelQuery()
 * @method static Builder|AccountEmailSettings newQuery()
 * @method static Builder|AccountEmailSettings query()
 * @method static Builder|AccountEmailSettings whereAccountId($value)
 * @method static Builder|AccountEmailSettings whereBccEmail($value)
 * @method static Builder|AccountEmailSettings whereCreatedAt($value)
 * @method static Builder|AccountEmailSettings whereDeletedAt($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectInvoice($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectPayment($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectProposal($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectQuote($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectReminder1($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectReminder2($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectReminder3($value)
 * @method static Builder|AccountEmailSettings whereEmailSubjectReminder4($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplateInvoice($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplatePayment($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplateProposal($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplateQuote($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplateReminder1($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplateReminder2($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplateReminder3($value)
 * @method static Builder|AccountEmailSettings whereEmailTemplateReminder4($value)
 * @method static Builder|AccountEmailSettings whereFrequencyIdReminder4($value)
 * @method static Builder|AccountEmailSettings whereId($value)
 * @method static Builder|AccountEmailSettings whereLateFee1Amount($value)
 * @method static Builder|AccountEmailSettings whereLateFee1Percent($value)
 * @method static Builder|AccountEmailSettings whereLateFee2Amount($value)
 * @method static Builder|AccountEmailSettings whereLateFee2Percent($value)
 * @method static Builder|AccountEmailSettings whereLateFee3Amount($value)
 * @method static Builder|AccountEmailSettings whereLateFee3Percent($value)
 * @method static Builder|AccountEmailSettings wherePublicId($value)
 * @method static Builder|AccountEmailSettings whereReplyToEmail($value)
 * @method static Builder|AccountEmailSettings whereUpdatedAt($value)
 * @method static Builder|AccountEmailSettings whereUserId($value)
 * @mixin Eloquent
 */
	class AccountEmailSettings extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class AccountGatewaySettings.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $gateway_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $min_limit
 * @property int|null $max_limit
 * @property float|null $fee_amount
 * @property float|null $fee_percent
 * @property string|null $fee_tax_name1
 * @property string|null $fee_tax_name2
 * @property float|null $fee_tax_rate1
 * @property float|null $fee_tax_rate2
 * @property-read GatewayType|null $gatewayType
 * @method static Builder|AccountGatewaySettings newModelQuery()
 * @method static Builder|AccountGatewaySettings newQuery()
 * @method static Builder|AccountGatewaySettings query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|AccountGatewaySettings whereAccountId($value)
 * @method static Builder|AccountGatewaySettings whereCreatedAt($value)
 * @method static Builder|AccountGatewaySettings whereDeletedAt($value)
 * @method static Builder|AccountGatewaySettings whereFeeAmount($value)
 * @method static Builder|AccountGatewaySettings whereFeePercent($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxName1($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxName2($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxRate1($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxRate2($value)
 * @method static Builder|AccountGatewaySettings whereGatewayTypeId($value)
 * @method static Builder|AccountGatewaySettings whereId($value)
 * @method static Builder|AccountGatewaySettings whereMaxLimit($value)
 * @method static Builder|AccountGatewaySettings whereMinLimit($value)
 * @method static Builder|AccountGatewaySettings wherePublicId($value)
 * @method static Builder|AccountGatewaySettings whereUpdatedAt($value)
 * @method static Builder|AccountGatewaySettings whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
	class AccountGatewaySettings extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class AccountGatewayToken.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $account_gateway_id
 * @property int|null $client_id
 * @property int|null $default_payment_method_id
 * @property string|null $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read AccountGateway|null $account_gateway
 * @property-read Contact|null $contact
 * @property-read PaymentMethod|null $default_payment_method
 * @property-read Collection|PaymentMethod[] $payment_methods
 * @property-read int|null $payment_methods_count
 * @method static Builder|AccountGatewayToken clientAndGateway($clientId, $accountGatewayId)
 * @method static Builder|AccountGatewayToken newModelQuery()
 * @method static Builder|AccountGatewayToken newQuery()
 * @method static Builder|AccountGatewayToken onlyTrashed()
 * @method static Builder|AccountGatewayToken query()
 * @method static Builder|AccountGatewayToken whereAccountGatewayId($value)
 * @method static Builder|AccountGatewayToken whereAccountId($value)
 * @method static Builder|AccountGatewayToken whereClientId($value)
 * @method static Builder|AccountGatewayToken whereContactId($value)
 * @method static Builder|AccountGatewayToken whereCreatedAt($value)
 * @method static Builder|AccountGatewayToken whereCreatedBy($value)
 * @method static Builder|AccountGatewayToken whereDefaultPaymentMethodId($value)
 * @method static Builder|AccountGatewayToken whereDeletedAt($value)
 * @method static Builder|AccountGatewayToken whereDeletedBy($value)
 * @method static Builder|AccountGatewayToken whereId($value)
 * @method static Builder|AccountGatewayToken wherePublicId($value)
 * @method static Builder|AccountGatewayToken whereToken($value)
 * @method static Builder|AccountGatewayToken whereUpdatedAt($value)
 * @method static Builder|AccountGatewayToken whereUpdatedBy($value)
 * @method static Builder|AccountGatewayToken whereUserId($value)
 * @method static Builder|AccountGatewayToken withTrashed()
 * @method static Builder|AccountGatewayToken withoutTrashed()
 * @mixin Eloquent
 */
	class AccountGatewayToken extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class AccountToken.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $token
 * @property string|null $name
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $user
 * @method static Builder|AccountToken newModelQuery()
 * @method static Builder|AccountToken newQuery()
 * @method static Builder|AccountToken onlyTrashed()
 * @method static Builder|AccountToken query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|AccountToken whereAccountId($value)
 * @method static Builder|AccountToken whereCreatedAt($value)
 * @method static Builder|AccountToken whereCreatedBy($value)
 * @method static Builder|AccountToken whereDeletedAt($value)
 * @method static Builder|AccountToken whereDeletedBy($value)
 * @method static Builder|AccountToken whereId($value)
 * @method static Builder|AccountToken whereIsDeleted($value)
 * @method static Builder|AccountToken whereName($value)
 * @method static Builder|AccountToken whereNotes($value)
 * @method static Builder|AccountToken wherePublicId($value)
 * @method static Builder|AccountToken whereToken($value)
 * @method static Builder|AccountToken whereUpdatedAt($value)
 * @method static Builder|AccountToken whereUpdatedBy($value)
 * @method static Builder|AccountToken whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|AccountToken withTrashed()
 * @method static Builder|AccountToken withoutTrashed()
 * @mixin Eloquent
 */
	class AccountToken extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ActivityListener.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $bill_id
 * @property int|null $client_id
 * @property int|null $contact_id
 * @property int|null $vendor_id
 * @property int|null $vendor_contact_id
 * @property int|null $payment_id
 * @property int|null $bill_payment_id
 * @property int|null $credit_id
 * @property int|null $bill_credit_id
 * @property int|null $invitation_id
 * @property int|null $bill_invitation_id
 * @property int|null $task_id
 * @property int|null $expense_id
 * @property string|null $json_backup
 * @property int $activity_type_id
 * @property float|null $adjustment
 * @property float|null $balance
 * @property int|null $token_id
 * @property string|null $ip
 * @property int $is_system
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Bill|null $bill
 * @property-read BillCredit|null $bill_credit
 * @property-read BillPayment|null $bill_payment
 * @property-read Client|null $client
 * @property-read Contact|null $contact
 * @property-read Credit|null $credit
 * @property-read Expense|null $expense
 * @property-read Invoice|null $invoice
 * @property-read Payment|null $payment
 * @property-read Task|null $task
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @property-read VendorContact|null $vendor_contact
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Activity whereAccountId($value)
 * @method static Builder|Activity whereActivityTypeId($value)
 * @method static Builder|Activity whereAdjustment($value)
 * @method static Builder|Activity whereBalance($value)
 * @method static Builder|Activity whereBillCreditId($value)
 * @method static Builder|Activity whereBillId($value)
 * @method static Builder|Activity whereBillInvitationId($value)
 * @method static Builder|Activity whereBillPaymentId($value)
 * @method static Builder|Activity whereClientId($value)
 * @method static Builder|Activity whereContactId($value)
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereCreatedBy($value)
 * @method static Builder|Activity whereCreditId($value)
 * @method static Builder|Activity whereDeletedAt($value)
 * @method static Builder|Activity whereDeletedBy($value)
 * @method static Builder|Activity whereExpenseId($value)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity whereInvitationId($value)
 * @method static Builder|Activity whereInvoiceId($value)
 * @method static Builder|Activity whereIp($value)
 * @method static Builder|Activity whereIsSystem($value)
 * @method static Builder|Activity whereJsonBackup($value)
 * @method static Builder|Activity whereNotes($value)
 * @method static Builder|Activity wherePaymentId($value)
 * @method static Builder|Activity wherePublicId($value)
 * @method static Builder|Activity whereTaskId($value)
 * @method static Builder|Activity whereTokenId($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 * @method static Builder|Activity whereUpdatedBy($value)
 * @method static Builder|Activity whereUserId($value)
 * @method static Builder|Activity whereVendorContactId($value)
 * @method static Builder|Activity whereVendorId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
	class Activity extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Affiliate.
 *
 * @property int $id
 * @property string|null $affiliate_key
 * @property string|null $name
 * @property string|null $payment_title
 * @property string|null $payment_subtitle
 * @property float|null $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Affiliate newModelQuery()
 * @method static Builder|Affiliate newQuery()
 * @method static Builder|Affiliate query()
 * @method static Builder|Affiliate whereAffiliateKey($value)
 * @method static Builder|Affiliate whereCreatedAt($value)
 * @method static Builder|Affiliate whereCreatedBy($value)
 * @method static Builder|Affiliate whereDeletedAt($value)
 * @method static Builder|Affiliate whereDeletedBy($value)
 * @method static Builder|Affiliate whereId($value)
 * @method static Builder|Affiliate whereName($value)
 * @method static Builder|Affiliate wherePaymentSubtitle($value)
 * @method static Builder|Affiliate wherePaymentTitle($value)
 * @method static Builder|Affiliate wherePrice($value)
 * @method static Builder|Affiliate whereUpdatedAt($value)
 * @method static Builder|Affiliate whereUpdatedBy($value)
 * @mixin Eloquent
 */
	class Affiliate extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Bank.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $name
 * @property string|null $remote_id
 * @property int $bank_library_id
 * @property string|null $config
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Bank newModelQuery()
 * @method static Builder|Bank newQuery()
 * @method static Builder|Bank query()
 * @method static Builder|Bank whereAccountId($value)
 * @method static Builder|Bank whereBankLibraryId($value)
 * @method static Builder|Bank whereConfig($value)
 * @method static Builder|Bank whereCreatedAt($value)
 * @method static Builder|Bank whereCreatedBy($value)
 * @method static Builder|Bank whereDeletedAt($value)
 * @method static Builder|Bank whereDeletedBy($value)
 * @method static Builder|Bank whereId($value)
 * @method static Builder|Bank whereName($value)
 * @method static Builder|Bank wherePublicId($value)
 * @method static Builder|Bank whereRemoteId($value)
 * @method static Builder|Bank whereUpdatedAt($value)
 * @method static Builder|Bank whereUpdatedBy($value)
 * @method static Builder|Bank whereUserId($value)
 * @mixin Eloquent
 */
	class Bank extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class BankAccount.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $bank_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $username
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $app_version
 * @property int|null $ofx_version
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Bank|null $bank
 * @property-read Collection|BankSubaccount[] $bank_subaccounts
 * @property-read int|null $bank_subaccounts_count
 * @method static Builder|BankAccount newModelQuery()
 * @method static Builder|BankAccount newQuery()
 * @method static Builder|BankAccount onlyTrashed()
 * @method static Builder|BankAccount query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|BankAccount whereAccountId($value)
 * @method static Builder|BankAccount whereAppVersion($value)
 * @method static Builder|BankAccount whereBankId($value)
 * @method static Builder|BankAccount whereCreatedAt($value)
 * @method static Builder|BankAccount whereCreatedBy($value)
 * @method static Builder|BankAccount whereDeletedAt($value)
 * @method static Builder|BankAccount whereDeletedBy($value)
 * @method static Builder|BankAccount whereId($value)
 * @method static Builder|BankAccount whereOfxVersion($value)
 * @method static Builder|BankAccount wherePublicId($value)
 * @method static Builder|BankAccount whereUpdatedAt($value)
 * @method static Builder|BankAccount whereUpdatedBy($value)
 * @method static Builder|BankAccount whereUserId($value)
 * @method static Builder|BankAccount whereUsername($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|BankAccount withTrashed()
 * @method static Builder|BankAccount withoutTrashed()
 * @mixin Eloquent
 */
	class BankAccount extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class BankSubaccount.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $bank_account_id
 * @property int|null $public_id
 * @property string|null $account_name
 * @property string|null $account_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read BankAccount|null $bank_account
 * @method static Builder|BankSubaccount newModelQuery()
 * @method static Builder|BankSubaccount newQuery()
 * @method static Builder|BankSubaccount onlyTrashed()
 * @method static Builder|BankSubaccount query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|BankSubaccount whereAccountId($value)
 * @method static Builder|BankSubaccount whereAccountName($value)
 * @method static Builder|BankSubaccount whereAccountNumber($value)
 * @method static Builder|BankSubaccount whereBankAccountId($value)
 * @method static Builder|BankSubaccount whereCreatedAt($value)
 * @method static Builder|BankSubaccount whereCreatedBy($value)
 * @method static Builder|BankSubaccount whereDeletedAt($value)
 * @method static Builder|BankSubaccount whereDeletedBy($value)
 * @method static Builder|BankSubaccount whereId($value)
 * @method static Builder|BankSubaccount wherePublicId($value)
 * @method static Builder|BankSubaccount whereUpdatedAt($value)
 * @method static Builder|BankSubaccount whereUpdatedBy($value)
 * @method static Builder|BankSubaccount whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|BankSubaccount withTrashed()
 * @method static Builder|BankSubaccount withoutTrashed()
 * @mixin Eloquent
 */
	class BankSubaccount extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class BillBillCredit.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $vendor_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property float $amount
 * @property float $balance
 * @property string|null $credit_date
 * @property string|null $credit_number
 * @property string|null $private_notes
 * @property string|null $public_notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Invoice $invoice
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @method static Builder|BillCredit newModelQuery()
 * @method static Builder|BillCredit newQuery()
 * @method static Builder|BillCredit onlyTrashed()
 * @method static Builder|BillCredit query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|BillCredit whereAccountId($value)
 * @method static Builder|BillCredit whereAmount($value)
 * @method static Builder|BillCredit whereBalance($value)
 * @method static Builder|BillCredit whereCreatedAt($value)
 * @method static Builder|BillCredit whereCreatedBy($value)
 * @method static Builder|BillCredit whereCreditDate($value)
 * @method static Builder|BillCredit whereCreditNumber($value)
 * @method static Builder|BillCredit whereDeletedAt($value)
 * @method static Builder|BillCredit whereDeletedBy($value)
 * @method static Builder|BillCredit whereId($value)
 * @method static Builder|BillCredit whereIsDeleted($value)
 * @method static Builder|BillCredit wherePrivateNotes($value)
 * @method static Builder|BillCredit wherePublicId($value)
 * @method static Builder|BillCredit wherePublicNotes($value)
 * @method static Builder|BillCredit whereUpdatedAt($value)
 * @method static Builder|BillCredit whereUpdatedBy($value)
 * @method static Builder|BillCredit whereUserId($value)
 * @method static Builder|BillCredit whereVendorId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|BillCredit withTrashed()
 * @method static Builder|BillCredit withoutTrashed()
 * @mixin Eloquent
 */
	class BillCredit extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class BillInvitation.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $bill_id
 * @property string|null $message_id
 * @property string|null $invitation_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $transaction_reference
 * @property string|null $sent_date
 * @property string|null $viewed_date
 * @property string|null $opened_date
 * @property string|null $email_error
 * @property string|null $signature_base64
 * @property string|null $signature_date
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read VendorContact|null $contact
 * @property-read Bill $invoice
 * @property-read User|null $user
 * @method static Builder|BillInvitation newModelQuery()
 * @method static Builder|BillInvitation newQuery()
 * @method static Builder|BillInvitation onlyTrashed()
 * @method static Builder|BillInvitation query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|BillInvitation whereAccountId($value)
 * @method static Builder|BillInvitation whereBillId($value)
 * @method static Builder|BillInvitation whereContactId($value)
 * @method static Builder|BillInvitation whereCreatedAt($value)
 * @method static Builder|BillInvitation whereCreatedBy($value)
 * @method static Builder|BillInvitation whereDeletedAt($value)
 * @method static Builder|BillInvitation whereDeletedBy($value)
 * @method static Builder|BillInvitation whereEmailError($value)
 * @method static Builder|BillInvitation whereId($value)
 * @method static Builder|BillInvitation whereInvitationKey($value)
 * @method static Builder|BillInvitation whereMessageId($value)
 * @method static Builder|BillInvitation whereOpenedDate($value)
 * @method static Builder|BillInvitation wherePublicId($value)
 * @method static Builder|BillInvitation whereSentDate($value)
 * @method static Builder|BillInvitation whereSignatureBase64($value)
 * @method static Builder|BillInvitation whereSignatureDate($value)
 * @method static Builder|BillInvitation whereTransactionReference($value)
 * @method static Builder|BillInvitation whereUpdatedAt($value)
 * @method static Builder|BillInvitation whereUpdatedBy($value)
 * @method static Builder|BillInvitation whereUserId($value)
 * @method static Builder|BillInvitation whereViewedDate($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|BillInvitation withTrashed()
 * @method static Builder|BillInvitation withoutTrashed()
 * @mixin Eloquent
 */
	class BillInvitation extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class BillItem.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $bill_id
 * @property int|null $product_id
 * @property int|null $warehouse_id
 * @property int|null $bill_item_type_id
 * @property string|null $product_key
 * @property float|null $cost
 * @property float|null $qty
 * @property float|null $demand_qty
 * @property float|null $qty_received
 * @property string|null $tax_name1
 * @property float|null $tax_rate1
 * @property string|null $tax_name2
 * @property float|null $tax_rate2
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property float|null $discount
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Bill|null $bill
 * @property-read Product|null $product
 * @property-read User|null $user
 * @method static Builder|BillItem newModelQuery()
 * @method static Builder|BillItem newQuery()
 * @method static Builder|BillItem onlyTrashed()
 * @method static Builder|BillItem query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|BillItem whereAccountId($value)
 * @method static Builder|BillItem whereBillId($value)
 * @method static Builder|BillItem whereBillItemTypeId($value)
 * @method static Builder|BillItem whereCost($value)
 * @method static Builder|BillItem whereCreatedAt($value)
 * @method static Builder|BillItem whereCreatedBy($value)
 * @method static Builder|BillItem whereCustomValue1($value)
 * @method static Builder|BillItem whereCustomValue2($value)
 * @method static Builder|BillItem whereDeletedAt($value)
 * @method static Builder|BillItem whereDeletedBy($value)
 * @method static Builder|BillItem whereDemandQty($value)
 * @method static Builder|BillItem whereDiscount($value)
 * @method static Builder|BillItem whereId($value)
 * @method static Builder|BillItem whereIsDeleted($value)
 * @method static Builder|BillItem whereNotes($value)
 * @method static Builder|BillItem whereProductId($value)
 * @method static Builder|BillItem whereProductKey($value)
 * @method static Builder|BillItem wherePublicId($value)
 * @method static Builder|BillItem whereQty($value)
 * @method static Builder|BillItem whereQtyReceived($value)
 * @method static Builder|BillItem whereTaxName1($value)
 * @method static Builder|BillItem whereTaxName2($value)
 * @method static Builder|BillItem whereTaxRate1($value)
 * @method static Builder|BillItem whereTaxRate2($value)
 * @method static Builder|BillItem whereUpdatedAt($value)
 * @method static Builder|BillItem whereUpdatedBy($value)
 * @method static Builder|BillItem whereUserId($value)
 * @method static Builder|BillItem whereWarehouseId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|BillItem withTrashed()
 * @method static Builder|BillItem withoutTrashed()
 * @mixin Eloquent
 */
	class BillItem extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class BillBillProposalInvitation.
 *
 * @property-read Account|null $account
 * @property-read VendorContact $contact
 * @property-read Proposal $proposal
 * @property-read User|null $user
 * @method static Builder|BillProposalInvitation newModelQuery()
 * @method static Builder|BillProposalInvitation newQuery()
 * @method static Builder|BillProposalInvitation onlyTrashed()
 * @method static Builder|BillProposalInvitation query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|BillProposalInvitation withTrashed()
 * @method static Builder|BillProposalInvitation withoutTrashed()
 * @mixin Eloquent
 */
	class BillProposalInvitation extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class BillStatus.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|BillStatus newModelQuery()
 * @method static Builder|BillStatus newQuery()
 * @method static Builder|BillStatus query()
 * @method static Builder|BillStatus whereAccountId($value)
 * @method static Builder|BillStatus whereCreatedAt($value)
 * @method static Builder|BillStatus whereCreatedBy($value)
 * @method static Builder|BillStatus whereDeletedAt($value)
 * @method static Builder|BillStatus whereDeletedBy($value)
 * @method static Builder|BillStatus whereId($value)
 * @method static Builder|BillStatus whereIsDeleted($value)
 * @method static Builder|BillStatus whereName($value)
 * @method static Builder|BillStatus whereNotes($value)
 * @method static Builder|BillStatus wherePublicId($value)
 * @method static Builder|BillStatus whereUpdatedAt($value)
 * @method static Builder|BillStatus whereUpdatedBy($value)
 * @method static Builder|BillStatus whereUserId($value)
 * @mixin Eloquent
 */
	class BillStatus extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class Branch.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int|null $user_id
 * @property int|null $company_id
 * @property int|null $location_id
 * @property int|null $warehouse_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|Bill[] $bills
 * @property-read int|null $bills_count
 * @property-read Collection|Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read Collection|ItemRequest[] $itemRequest
 * @property-read int|null $item_request_count
 * @property-read Location|null $location
 * @property-read User $manager
 * @property-read User|null $user
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @property-read Warehouse|null $warehouse
 * @method static Builder|Branch newModelQuery()
 * @method static Builder|Branch newQuery()
 * @method static Builder|Branch onlyTrashed()
 * @method static Builder|Branch query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Branch whereAccountId($value)
 * @method static Builder|Branch whereCompanyId($value)
 * @method static Builder|Branch whereCreatedAt($value)
 * @method static Builder|Branch whereCreatedBy($value)
 * @method static Builder|Branch whereDeletedAt($value)
 * @method static Builder|Branch whereDeletedBy($value)
 * @method static Builder|Branch whereId($value)
 * @method static Builder|Branch whereIsDeleted($value)
 * @method static Builder|Branch whereLocationId($value)
 * @method static Builder|Branch whereName($value)
 * @method static Builder|Branch whereNotes($value)
 * @method static Builder|Branch wherePublicId($value)
 * @method static Builder|Branch whereUpdatedAt($value)
 * @method static Builder|Branch whereUpdatedBy($value)
 * @method static Builder|Branch whereUserId($value)
 * @method static Builder|Branch whereWarehouseId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Branch withTrashed()
 * @method static Builder|Branch withoutTrashed()
 * @mixin Eloquent
 */
	class Branch extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
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
 * @method static Builder|Client isInvoiceAllowed()
 * @method static Builder|Client newModelQuery()
 * @method static Builder|Client newQuery()
 * @method static Builder|Client onlyTrashed()
 * @method static Builder|Client query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Client whereAccountId($value)
 * @method static Builder|Client whereAddress1($value)
 * @method static Builder|Client whereAddress2($value)
 * @method static Builder|Client whereBalance($value)
 * @method static Builder|Client whereBillingAddress1($value)
 * @method static Builder|Client whereBillingAddress2($value)
 * @method static Builder|Client whereBillingCity($value)
 * @method static Builder|Client whereBillingPostalCode($value)
 * @method static Builder|Client whereBillingState($value)
 * @method static Builder|Client whereCity($value)
 * @method static Builder|Client whereClientTypeId($value)
 * @method static Builder|Client whereCountryId($value)
 * @method static Builder|Client whereCreatedAt($value)
 * @method static Builder|Client whereCreatedBy($value)
 * @method static Builder|Client whereCreditNumberCounter($value)
 * @method static Builder|Client whereCurrencyId($value)
 * @method static Builder|Client whereCustomMessages($value)
 * @method static Builder|Client whereCustomValue1($value)
 * @method static Builder|Client whereCustomValue2($value)
 * @method static Builder|Client whereDeletedAt($value)
 * @method static Builder|Client whereDeletedBy($value)
 * @method static Builder|Client whereHoldReasonId($value)
 * @method static Builder|Client whereId($value)
 * @method static Builder|Client whereIdNumber($value)
 * @method static Builder|Client whereIndustryId($value)
 * @method static Builder|Client whereInvoiceNumberCounter($value)
 * @method static Builder|Client whereIsDeleted($value)
 * @method static Builder|Client whereLanguageId($value)
 * @method static Builder|Client whereLastLogin($value)
 * @method static Builder|Client whereName($value)
 * @method static Builder|Client wherePaidToDate($value)
 * @method static Builder|Client wherePaymentTerms($value)
 * @method static Builder|Client wherePostalCode($value)
 * @method static Builder|Client wherePrivateNotes($value)
 * @method static Builder|Client wherePublicId($value)
 * @method static Builder|Client wherePublicNotes($value)
 * @method static Builder|Client whereQuoteNumberCounter($value)
 * @method static Builder|Client whereSaleTypeId($value)
 * @method static Builder|Client whereSendReminders($value)
 * @method static Builder|Client whereShippingAddress1($value)
 * @method static Builder|Client whereShippingAddress2($value)
 * @method static Builder|Client whereShippingCity($value)
 * @method static Builder|Client whereShippingCountryId($value)
 * @method static Builder|Client whereShippingPostalCode($value)
 * @method static Builder|Client whereShippingState($value)
 * @method static Builder|Client whereShowTasksInPortal($value)
 * @method static Builder|Client whereSizeId($value)
 * @method static Builder|Client whereState($value)
 * @method static Builder|Client whereTaskRate($value)
 * @method static Builder|Client whereUpdatedAt($value)
 * @method static Builder|Client whereUpdatedBy($value)
 * @method static Builder|Client whereUserId($value)
 * @method static Builder|Client whereVatNumber($value)
 * @method static Builder|Client whereWebsite($value)
 * @method static Builder|Client whereWorkPhone($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Client withTrashed()
 * @method static Builder|Client withoutTrashed()
 * @mixin Eloquent
 */
	class Client extends Eloquent {}
}

namespace App\Models\Common{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class AccountGateway.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $gateway_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $config
 * @property int|null $accepted_credit_cards
 * @property int $show_address
 * @property int $update_address
 * @property int $require_cvv
 * @property int $show_shipping_address
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Gateway|null $gateway
 * @method static Builder|AccountGateway newModelQuery()
 * @method static Builder|AccountGateway newQuery()
 * @method static Builder|AccountGateway onlyTrashed()
 * @method static Builder|AccountGateway query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|AccountGateway whereAcceptedCreditCards($value)
 * @method static Builder|AccountGateway whereAccountId($value)
 * @method static Builder|AccountGateway whereConfig($value)
 * @method static Builder|AccountGateway whereCreatedAt($value)
 * @method static Builder|AccountGateway whereCreatedBy($value)
 * @method static Builder|AccountGateway whereDeletedAt($value)
 * @method static Builder|AccountGateway whereDeletedBy($value)
 * @method static Builder|AccountGateway whereGatewayId($value)
 * @method static Builder|AccountGateway whereId($value)
 * @method static Builder|AccountGateway whereName($value)
 * @method static Builder|AccountGateway wherePublicId($value)
 * @method static Builder|AccountGateway whereRequireCvv($value)
 * @method static Builder|AccountGateway whereShowAddress($value)
 * @method static Builder|AccountGateway whereShowShippingAddress($value)
 * @method static Builder|AccountGateway whereUpdateAddress($value)
 * @method static Builder|AccountGateway whereUpdatedAt($value)
 * @method static Builder|AccountGateway whereUpdatedBy($value)
 * @method static Builder|AccountGateway whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|AccountGateway withTrashed()
 * @method static Builder|AccountGateway withoutTrashed()
 * @mixin Eloquent
 */
	class AccountGateway extends Eloquent {}
}

namespace App\Models\Common{

    use Eloquent;

    /**
 * Class EntityModel.
 *
 * @method static Builder|EntityModel newModelQuery()
 * @method static Builder|EntityModel newQuery()
 * @method static Builder|EntityModel query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
	class EntityModel extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class Company.
 *
 * @property int $id
 * @property int|null $payment_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property string|null $plan
 * @property string|null $plan_term
 * @property string|null $plan_started
 * @property string|null $plan_paid
 * @property string|null $plan_expires
 * @property string|null $trial_started
 * @property string|null $trial_plan
 * @property string|null $pending_plan
 * @property string|null $pending_term
 * @property float|null $plan_price
 * @property float|null $pending_plan_price
 * @property int $num_users
 * @property int $pending_num_users
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property float $discount
 * @property Carbon|null $discount_expires
 * @property Carbon|null $promo_expires
 * @property string|null $bluevine_status
 * @property string|null $referral_code
 * @property int|null $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Account[] $accounts
 * @property-read int|null $accounts_count
 * @property-read Payment|null $payment
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company onlyTrashed()
 * @method static Builder|Company query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Company whereAccountId($value)
 * @method static Builder|Company whereBluevineStatus($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereCreatedBy($value)
 * @method static Builder|Company whereDeletedAt($value)
 * @method static Builder|Company whereDeletedBy($value)
 * @method static Builder|Company whereDiscount($value)
 * @method static Builder|Company whereDiscountExpires($value)
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereIsDeleted($value)
 * @method static Builder|Company whereNumUsers($value)
 * @method static Builder|Company wherePaymentId($value)
 * @method static Builder|Company wherePendingNumUsers($value)
 * @method static Builder|Company wherePendingPlan($value)
 * @method static Builder|Company wherePendingPlanPrice($value)
 * @method static Builder|Company wherePendingTerm($value)
 * @method static Builder|Company wherePlan($value)
 * @method static Builder|Company wherePlanExpires($value)
 * @method static Builder|Company wherePlanPaid($value)
 * @method static Builder|Company wherePlanPrice($value)
 * @method static Builder|Company wherePlanStarted($value)
 * @method static Builder|Company wherePlanTerm($value)
 * @method static Builder|Company wherePromoExpires($value)
 * @method static Builder|Company wherePublicId($value)
 * @method static Builder|Company whereReferralCode($value)
 * @method static Builder|Company whereTrialPlan($value)
 * @method static Builder|Company whereTrialStarted($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @method static Builder|Company whereUpdatedBy($value)
 * @method static Builder|Company whereUserId($value)
 * @method static Builder|Company whereUtmCampaign($value)
 * @method static Builder|Company whereUtmContent($value)
 * @method static Builder|Company whereUtmMedium($value)
 * @method static Builder|Company whereUtmSource($value)
 * @method static Builder|Company whereUtmTerm($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Company withTrashed()
 * @method static Builder|Company withoutTrashed()
 * @mixin Eloquent
 */
	class Company extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Contracts\Auth\Authenticatable;
    use Illuminate\Contracts\Auth\CanResetPassword;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Contact.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property string|null $contact_key
 * @property string|null $bot_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_primary
 * @property int $send_invoice
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $phone
 * @property string|null $last_login
 * @property string|null $banned_until
 * @property int|null $confirmation_code
 * @property int|null $remember_token
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read mixed $link
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $user
 * @method static Builder|Contact newModelQuery()
 * @method static Builder|Contact newQuery()
 * @method static Builder|Contact onlyTrashed()
 * @method static Builder|Contact query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Contact whereAccountId($value)
 * @method static Builder|Contact whereBannedUntil($value)
 * @method static Builder|Contact whereBotUserId($value)
 * @method static Builder|Contact whereClientId($value)
 * @method static Builder|Contact whereConfirmationCode($value)
 * @method static Builder|Contact whereContactKey($value)
 * @method static Builder|Contact whereCreatedAt($value)
 * @method static Builder|Contact whereCreatedBy($value)
 * @method static Builder|Contact whereCustomValue1($value)
 * @method static Builder|Contact whereCustomValue2($value)
 * @method static Builder|Contact whereDeletedAt($value)
 * @method static Builder|Contact whereDeletedBy($value)
 * @method static Builder|Contact whereEmail($value)
 * @method static Builder|Contact whereFirstName($value)
 * @method static Builder|Contact whereId($value)
 * @method static Builder|Contact whereIsPrimary($value)
 * @method static Builder|Contact whereLastLogin($value)
 * @method static Builder|Contact whereLastName($value)
 * @method static Builder|Contact wherePassword($value)
 * @method static Builder|Contact wherePhone($value)
 * @method static Builder|Contact wherePublicId($value)
 * @method static Builder|Contact whereRememberToken($value)
 * @method static Builder|Contact whereSendInvoice($value)
 * @method static Builder|Contact whereUpdatedAt($value)
 * @method static Builder|Contact whereUpdatedBy($value)
 * @method static Builder|Contact whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Contact withTrashed()
 * @method static Builder|Contact withoutTrashed()
 * @mixin Eloquent
 */
	class Contact extends Eloquent implements Authenticatable, CanResetPassword {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Country.
 *
 * @property int $id
 * @property string|null $capital
 * @property string|null $citizenship
 * @property string|null $country_code
 * @property string|null $currency
 * @property string|null $currency_code
 * @property string|null $currency_sub_unit
 * @property string|null $full_name
 * @property string|null $iso_3166_2
 * @property string|null $iso_3166_3
 * @property string|null $name
 * @property string|null $region_code
 * @property string|null $sub_region_code
 * @property int $eea
 * @property bool $swap_postal_code
 * @property bool $swap_currency_symbol
 * @property string|null $thousand_separator
 * @property string|null $decimal_separator
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @method static Builder|Country newModelQuery()
 * @method static Builder|Country newQuery()
 * @method static Builder|Country query()
 * @method static Builder|Country whereCapital($value)
 * @method static Builder|Country whereCitizenship($value)
 * @method static Builder|Country whereCountryCode($value)
 * @method static Builder|Country whereCreatedAt($value)
 * @method static Builder|Country whereCreatedBy($value)
 * @method static Builder|Country whereCurrency($value)
 * @method static Builder|Country whereCurrencyCode($value)
 * @method static Builder|Country whereCurrencySubUnit($value)
 * @method static Builder|Country whereDecimalSeparator($value)
 * @method static Builder|Country whereDeletedAt($value)
 * @method static Builder|Country whereDeletedBy($value)
 * @method static Builder|Country whereEea($value)
 * @method static Builder|Country whereFullName($value)
 * @method static Builder|Country whereId($value)
 * @method static Builder|Country whereIso31662($value)
 * @method static Builder|Country whereIso31663($value)
 * @method static Builder|Country whereName($value)
 * @method static Builder|Country whereRegionCode($value)
 * @method static Builder|Country whereSubRegionCode($value)
 * @method static Builder|Country whereSwapCurrencySymbol($value)
 * @method static Builder|Country whereSwapPostalCode($value)
 * @method static Builder|Country whereThousandSeparator($value)
 * @method static Builder|Country whereUpdatedAt($value)
 * @method static Builder|Country whereUpdatedBy($value)
 * @mixin Eloquent
 */
	class Country extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Credit.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $client_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property float $amount
 * @property float $balance
 * @property string|null $credit_date
 * @property string|null $credit_number
 * @property string|null $private_notes
 * @property string|null $public_notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read Invoice $invoice
 * @property-read User|null $user
 * @method static Builder|Credit newModelQuery()
 * @method static Builder|Credit newQuery()
 * @method static Builder|Credit onlyTrashed()
 * @method static Builder|Credit query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Credit whereAccountId($value)
 * @method static Builder|Credit whereAmount($value)
 * @method static Builder|Credit whereBalance($value)
 * @method static Builder|Credit whereClientId($value)
 * @method static Builder|Credit whereCreatedAt($value)
 * @method static Builder|Credit whereCreatedBy($value)
 * @method static Builder|Credit whereCreditDate($value)
 * @method static Builder|Credit whereCreditNumber($value)
 * @method static Builder|Credit whereDeletedAt($value)
 * @method static Builder|Credit whereDeletedBy($value)
 * @method static Builder|Credit whereId($value)
 * @method static Builder|Credit whereIsDeleted($value)
 * @method static Builder|Credit wherePrivateNotes($value)
 * @method static Builder|Credit wherePublicId($value)
 * @method static Builder|Credit wherePublicNotes($value)
 * @method static Builder|Credit whereUpdatedAt($value)
 * @method static Builder|Credit whereUpdatedBy($value)
 * @method static Builder|Credit whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Credit withTrashed()
 * @method static Builder|Credit withoutTrashed()
 * @mixin Eloquent
 */
	class Credit extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Currency.
 *
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property string $precision
 * @property string $thousand_separator
 * @property string $decimal_separator
 * @property string $code
 * @property bool $swap_currency_symbol
 * @property float|null $exchange_rate
 * @method static Builder|Currency newModelQuery()
 * @method static Builder|Currency newQuery()
 * @method static Builder|Currency query()
 * @method static Builder|Currency whereCode($value)
 * @method static Builder|Currency whereDecimalSeparator($value)
 * @method static Builder|Currency whereExchangeRate($value)
 * @method static Builder|Currency whereId($value)
 * @method static Builder|Currency whereName($value)
 * @method static Builder|Currency wherePrecision($value)
 * @method static Builder|Currency whereSwapCurrencySymbol($value)
 * @method static Builder|Currency whereSymbol($value)
 * @method static Builder|Currency whereThousandSeparator($value)
 * @mixin Eloquent
 */
	class Currency extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class Dashboard.
 *
 * @property-read Account $account
 * @property-read User $user
 * @method static Builder|Dashboard newModelQuery()
 * @method static Builder|Dashboard newQuery()
 * @method static Builder|Dashboard onlyTrashed()
 * @method static Builder|Dashboard query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Dashboard withTrashed()
 * @method static Builder|Dashboard withoutTrashed()
 * @mixin Eloquent
 */
	class Dashboard extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class DateFormat.
 *
 * @property int $id
 * @property string|null $format
 * @property string|null $picker_format
 * @property string|null $format_moment
 * @property string|null $format_dart
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|DateFormat newModelQuery()
 * @method static Builder|DateFormat newQuery()
 * @method static Builder|DateFormat query()
 * @method static Builder|DateFormat whereCreatedAt($value)
 * @method static Builder|DateFormat whereDeletedAt($value)
 * @method static Builder|DateFormat whereFormat($value)
 * @method static Builder|DateFormat whereFormatDart($value)
 * @method static Builder|DateFormat whereFormatMoment($value)
 * @method static Builder|DateFormat whereId($value)
 * @method static Builder|DateFormat wherePickerFormat($value)
 * @method static Builder|DateFormat whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class DateFormat extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class DatetimeFormat.
 *
 * @property int $id
 * @property string|null $format
 * @property string|null $format_moment
 * @property string|null $format_dart
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|DatetimeFormat newModelQuery()
 * @method static Builder|DatetimeFormat newQuery()
 * @method static Builder|DatetimeFormat query()
 * @method static Builder|DatetimeFormat whereCreatedAt($value)
 * @method static Builder|DatetimeFormat whereDeletedAt($value)
 * @method static Builder|DatetimeFormat whereFormat($value)
 * @method static Builder|DatetimeFormat whereFormatDart($value)
 * @method static Builder|DatetimeFormat whereFormatMoment($value)
 * @method static Builder|DatetimeFormat whereId($value)
 * @method static Builder|DatetimeFormat whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class DatetimeFormat extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property string|null $name
 * @method static Builder|DbServer newModelQuery()
 * @method static Builder|DbServer newQuery()
 * @method static Builder|DbServer query()
 * @method static Builder|DbServer whereId($value)
 * @method static Builder|DbServer whereName($value)
 * @mixin Eloquent
 */
	class DbServer extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Document.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $bill_id
 * @property int|null $expense_id
 * @property string|null $document_key
 * @property string|null $path
 * @property string|null $preview
 * @property string|null $name
 * @property string|null $type
 * @property string|null $disk
 * @property string|null $hash
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $is_default
 * @property int $is_proposal
 * @property string $created_by
 * @property string $updated_by
 * @property string $deleted_by
 * @property-read Bill $BILL
 * @property-read Account|null $account
 * @property-read Expense|null $expense
 * @property-read Invoice|null $invoice
 * @property-read User|null $user
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document proposalImages()
 * @method static Builder|Document query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Document whereAccountId($value)
 * @method static Builder|Document whereBillId($value)
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereCreatedBy($value)
 * @method static Builder|Document whereDeletedAt($value)
 * @method static Builder|Document whereDeletedBy($value)
 * @method static Builder|Document whereDisk($value)
 * @method static Builder|Document whereDocumentKey($value)
 * @method static Builder|Document whereExpenseId($value)
 * @method static Builder|Document whereHash($value)
 * @method static Builder|Document whereHeight($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereInvoiceId($value)
 * @method static Builder|Document whereIsDefault($value)
 * @method static Builder|Document whereIsProposal($value)
 * @method static Builder|Document whereName($value)
 * @method static Builder|Document wherePath($value)
 * @method static Builder|Document wherePreview($value)
 * @method static Builder|Document wherePublicId($value)
 * @method static Builder|Document whereSize($value)
 * @method static Builder|Document whereType($value)
 * @method static Builder|Document whereUpdatedAt($value)
 * @method static Builder|Document whereUpdatedBy($value)
 * @method static Builder|Document whereUserId($value)
 * @method static Builder|Document whereWidth($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
	class Document extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Expense.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property int|null $invoice_currency_id
 * @property int|null $expense_currency_id
 * @property int|null $expense_category_id
 * @property int|null $payment_type_id
 * @property int|null $invoice_id
 * @property int|null $bill_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $vendor_id
 * @property string|null $transaction_id
 * @property int|null $recurring_expense_id
 * @property int|null $bank_id
 * @property int $is_deleted
 * @property float $amount
 * @property float $exchange_rate
 * @property string|null $expense_date
 * @property string|null $private_notes
 * @property string|null $public_notes
 * @property int $should_be_invoiced
 * @property string|null $tax_name1
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property float|null $tax_rate1
 * @property string|null $tax_name2
 * @property float|null $tax_rate2
 * @property string|null $payment_date
 * @property string|null $transaction_reference
 * @property int|null $invoice_documents
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read Collection|Document[] $documents
 * @property-read int|null $documents_count
 * @property-read ExpenseCategory|null $expense_category
 * @property-read Invoice|null $invoice
 * @property-read PaymentType|null $payment_type
 * @property-read RecurringExpense|null $recurring_expense
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @method static Builder|Expense bankId($bankdId = null)
 * @method static Builder|Expense dateRange($startDate, $endDate)
 * @method static Builder|Expense newModelQuery()
 * @method static Builder|Expense newQuery()
 * @method static Builder|Expense onlyTrashed()
 * @method static Builder|Expense query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Expense whereAccountId($value)
 * @method static Builder|Expense whereAmount($value)
 * @method static Builder|Expense whereBankId($value)
 * @method static Builder|Expense whereBillId($value)
 * @method static Builder|Expense whereClientId($value)
 * @method static Builder|Expense whereCreatedAt($value)
 * @method static Builder|Expense whereCreatedBy($value)
 * @method static Builder|Expense whereCustomValue1($value)
 * @method static Builder|Expense whereCustomValue2($value)
 * @method static Builder|Expense whereDeletedAt($value)
 * @method static Builder|Expense whereDeletedBy($value)
 * @method static Builder|Expense whereExchangeRate($value)
 * @method static Builder|Expense whereExpenseCategoryId($value)
 * @method static Builder|Expense whereExpenseCurrencyId($value)
 * @method static Builder|Expense whereExpenseDate($value)
 * @method static Builder|Expense whereId($value)
 * @method static Builder|Expense whereInvoiceCurrencyId($value)
 * @method static Builder|Expense whereInvoiceDocuments($value)
 * @method static Builder|Expense whereInvoiceId($value)
 * @method static Builder|Expense whereIsDeleted($value)
 * @method static Builder|Expense wherePaymentDate($value)
 * @method static Builder|Expense wherePaymentTypeId($value)
 * @method static Builder|Expense wherePrivateNotes($value)
 * @method static Builder|Expense wherePublicId($value)
 * @method static Builder|Expense wherePublicNotes($value)
 * @method static Builder|Expense whereRecurringExpenseId($value)
 * @method static Builder|Expense whereShouldBeInvoiced($value)
 * @method static Builder|Expense whereTaxName1($value)
 * @method static Builder|Expense whereTaxName2($value)
 * @method static Builder|Expense whereTaxRate1($value)
 * @method static Builder|Expense whereTaxRate2($value)
 * @method static Builder|Expense whereTransactionId($value)
 * @method static Builder|Expense whereTransactionReference($value)
 * @method static Builder|Expense whereUpdatedAt($value)
 * @method static Builder|Expense whereUpdatedBy($value)
 * @method static Builder|Expense whereUserId($value)
 * @method static Builder|Expense whereVendorId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Expense withTrashed()
 * @method static Builder|Expense withoutTrashed()
 * @mixin Eloquent
 */
	class Expense extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Expense $expense
 * @method static Builder|ExpenseCategory newModelQuery()
 * @method static Builder|ExpenseCategory newQuery()
 * @method static Builder|ExpenseCategory onlyTrashed()
 * @method static Builder|ExpenseCategory query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ExpenseCategory whereAccountId($value)
 * @method static Builder|ExpenseCategory whereCreatedAt($value)
 * @method static Builder|ExpenseCategory whereCreatedBy($value)
 * @method static Builder|ExpenseCategory whereDeletedAt($value)
 * @method static Builder|ExpenseCategory whereDeletedBy($value)
 * @method static Builder|ExpenseCategory whereId($value)
 * @method static Builder|ExpenseCategory whereIsDeleted($value)
 * @method static Builder|ExpenseCategory whereName($value)
 * @method static Builder|ExpenseCategory whereNotes($value)
 * @method static Builder|ExpenseCategory wherePublicId($value)
 * @method static Builder|ExpenseCategory whereUpdatedAt($value)
 * @method static Builder|ExpenseCategory whereUpdatedBy($value)
 * @method static Builder|ExpenseCategory whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ExpenseCategory withTrashed()
 * @method static Builder|ExpenseCategory withoutTrashed()
 * @mixin Eloquent
 */
	class ExpenseCategory extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Font.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $folder
 * @property string|null $css_stack
 * @property int $css_weight
 * @property string|null $google_font
 * @property string|null $normal
 * @property string|null $bold
 * @property string|null $italics
 * @property string|null $bolditalics
 * @property int $sort_order
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Font newModelQuery()
 * @method static Builder|Font newQuery()
 * @method static Builder|Font query()
 * @method static Builder|Font whereBold($value)
 * @method static Builder|Font whereBolditalics($value)
 * @method static Builder|Font whereCreatedAt($value)
 * @method static Builder|Font whereCssStack($value)
 * @method static Builder|Font whereCssWeight($value)
 * @method static Builder|Font whereDeletedAt($value)
 * @method static Builder|Font whereFolder($value)
 * @method static Builder|Font whereGoogleFont($value)
 * @method static Builder|Font whereId($value)
 * @method static Builder|Font whereItalics($value)
 * @method static Builder|Font whereName($value)
 * @method static Builder|Font whereNormal($value)
 * @method static Builder|Font whereSortOrder($value)
 * @method static Builder|Font whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class Font extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Frequency.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $date_interval
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Frequency newModelQuery()
 * @method static Builder|Frequency newQuery()
 * @method static Builder|Frequency query()
 * @method static Builder|Frequency whereCreatedAt($value)
 * @method static Builder|Frequency whereDateInterval($value)
 * @method static Builder|Frequency whereDeletedAt($value)
 * @method static Builder|Frequency whereId($value)
 * @method static Builder|Frequency whereName($value)
 * @method static Builder|Frequency whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class Frequency extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Gateway.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $payment_library_id
 * @property string|null $name
 * @property string|null $provider
 * @property int $visible
 * @property int $sort_order
 * @property int $recommended
 * @property string|null $site_url
 * @property int $is_offsite
 * @property int $is_secure
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Gateway newModelQuery()
 * @method static Builder|Gateway newQuery()
 * @method static Builder|Gateway primary($accountGatewaysIds)
 * @method static Builder|Gateway query()
 * @method static Builder|Gateway secondary($accountGatewaysIds)
 * @method static Builder|Gateway whereAccountId($value)
 * @method static Builder|Gateway whereCreatedAt($value)
 * @method static Builder|Gateway whereCreatedBy($value)
 * @method static Builder|Gateway whereDeletedAt($value)
 * @method static Builder|Gateway whereDeletedBy($value)
 * @method static Builder|Gateway whereId($value)
 * @method static Builder|Gateway whereIsOffsite($value)
 * @method static Builder|Gateway whereIsSecure($value)
 * @method static Builder|Gateway whereName($value)
 * @method static Builder|Gateway wherePaymentLibraryId($value)
 * @method static Builder|Gateway whereProvider($value)
 * @method static Builder|Gateway wherePublicId($value)
 * @method static Builder|Gateway whereRecommended($value)
 * @method static Builder|Gateway whereSiteUrl($value)
 * @method static Builder|Gateway whereSortOrder($value)
 * @method static Builder|Gateway whereUpdatedAt($value)
 * @method static Builder|Gateway whereUpdatedBy($value)
 * @method static Builder|Gateway whereUserId($value)
 * @method static Builder|Gateway whereVisible($value)
 * @mixin Eloquent
 */
	class Gateway extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class GatewayType.
 *
 * @property int $id
 * @property string $alias
 * @property string $name
 * @method static Builder|GatewayType newModelQuery()
 * @method static Builder|GatewayType newQuery()
 * @method static Builder|GatewayType query()
 * @method static Builder|GatewayType whereAlias($value)
 * @method static Builder|GatewayType whereId($value)
 * @method static Builder|GatewayType whereName($value)
 * @mixin Eloquent
 */
	class GatewayType extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Model Store.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $allow_invoice
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read User|null $user
 * @method static Builder|HoldReason newModelQuery()
 * @method static Builder|HoldReason newQuery()
 * @method static Builder|HoldReason onlyTrashed()
 * @method static Builder|HoldReason query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|HoldReason whereAccountId($value)
 * @method static Builder|HoldReason whereAllowInvoice($value)
 * @method static Builder|HoldReason whereCreatedAt($value)
 * @method static Builder|HoldReason whereCreatedBy($value)
 * @method static Builder|HoldReason whereDeletedAt($value)
 * @method static Builder|HoldReason whereDeletedBy($value)
 * @method static Builder|HoldReason whereId($value)
 * @method static Builder|HoldReason whereIsDeleted($value)
 * @method static Builder|HoldReason whereName($value)
 * @method static Builder|HoldReason whereNotes($value)
 * @method static Builder|HoldReason wherePublicId($value)
 * @method static Builder|HoldReason whereUpdatedAt($value)
 * @method static Builder|HoldReason whereUpdatedBy($value)
 * @method static Builder|HoldReason whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|HoldReason withTrashed()
 * @method static Builder|HoldReason withoutTrashed()
 * @mixin Eloquent
 */
	class HoldReason extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Industry.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Industry newModelQuery()
 * @method static Builder|Industry newQuery()
 * @method static Builder|Industry query()
 * @method static Builder|Industry whereCreatedAt($value)
 * @method static Builder|Industry whereCreatedBy($value)
 * @method static Builder|Industry whereDeletedAt($value)
 * @method static Builder|Industry whereDeletedBy($value)
 * @method static Builder|Industry whereId($value)
 * @method static Builder|Industry whereName($value)
 * @method static Builder|Industry whereUpdatedAt($value)
 * @method static Builder|Industry whereUpdatedBy($value)
 * @mixin Eloquent
 */
	class Industry extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * View Model Class Inventory.
 *
 * @method static Builder|Inventory newModelQuery()
 * @method static Builder|Inventory newQuery()
 * @method static Builder|Inventory query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
	class Inventory extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Invitation.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $invoice_id
 * @property string|null $message_id
 * @property string|null $invitation_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $transaction_reference
 * @property string|null $sent_date
 * @property string|null $viewed_date
 * @property string|null $opened_date
 * @property string|null $email_error
 * @property string|null $signature_base64
 * @property string|null $signature_date
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Contact|null $contact
 * @property-read Invoice|null $invoice
 * @property-read User|null $user
 * @method static Builder|Invitation newModelQuery()
 * @method static Builder|Invitation newQuery()
 * @method static Builder|Invitation onlyTrashed()
 * @method static Builder|Invitation query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Invitation whereAccountId($value)
 * @method static Builder|Invitation whereContactId($value)
 * @method static Builder|Invitation whereCreatedAt($value)
 * @method static Builder|Invitation whereCreatedBy($value)
 * @method static Builder|Invitation whereDeletedAt($value)
 * @method static Builder|Invitation whereDeletedBy($value)
 * @method static Builder|Invitation whereEmailError($value)
 * @method static Builder|Invitation whereId($value)
 * @method static Builder|Invitation whereInvitationKey($value)
 * @method static Builder|Invitation whereInvoiceId($value)
 * @method static Builder|Invitation whereMessageId($value)
 * @method static Builder|Invitation whereOpenedDate($value)
 * @method static Builder|Invitation wherePublicId($value)
 * @method static Builder|Invitation whereSentDate($value)
 * @method static Builder|Invitation whereSignatureBase64($value)
 * @method static Builder|Invitation whereSignatureDate($value)
 * @method static Builder|Invitation whereTransactionReference($value)
 * @method static Builder|Invitation whereUpdatedAt($value)
 * @method static Builder|Invitation whereUpdatedBy($value)
 * @method static Builder|Invitation whereUserId($value)
 * @method static Builder|Invitation whereViewedDate($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Invitation withTrashed()
 * @method static Builder|Invitation withoutTrashed()
 * @mixin Eloquent
 */
	class Invitation extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

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
 * @method static Builder|Invoice dateRange($startDate, $endDate)
 * @method static Builder|Invoice invoiceType($typeId)
 * @method static Builder|Invoice invoices()
 * @method static Builder|Invoice newModelQuery()
 * @method static Builder|Invoice newQuery()
 * @method static Builder|Invoice onlyTrashed()
 * @method static Builder|Invoice query()
 * @method static Builder|Invoice quotes()
 * @method static Builder|Invoice recurring()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Invoice statusIds($statusIds)
 * @method static Builder|Invoice unapprovedQuotes($includeInvoiceId = false)
 * @method static Builder|Invoice whereAccountId($value)
 * @method static Builder|Invoice whereAmount($value)
 * @method static Builder|Invoice whereAutoBill($value)
 * @method static Builder|Invoice whereBalance($value)
 * @method static Builder|Invoice whereBranchId($value)
 * @method static Builder|Invoice whereClientEnableAutoBill($value)
 * @method static Builder|Invoice whereClientId($value)
 * @method static Builder|Invoice whereCreatedAt($value)
 * @method static Builder|Invoice whereCreatedBy($value)
 * @method static Builder|Invoice whereCustomTaxes1($value)
 * @method static Builder|Invoice whereCustomTaxes2($value)
 * @method static Builder|Invoice whereCustomTextValue1($value)
 * @method static Builder|Invoice whereCustomTextValue2($value)
 * @method static Builder|Invoice whereCustomValue1($value)
 * @method static Builder|Invoice whereCustomValue2($value)
 * @method static Builder|Invoice whereDeletedAt($value)
 * @method static Builder|Invoice whereDeletedBy($value)
 * @method static Builder|Invoice whereDiscount($value)
 * @method static Builder|Invoice whereDueDate($value)
 * @method static Builder|Invoice whereEndDate($value)
 * @method static Builder|Invoice whereFrequencyId($value)
 * @method static Builder|Invoice whereHasExpenses($value)
 * @method static Builder|Invoice whereHasTasks($value)
 * @method static Builder|Invoice whereId($value)
 * @method static Builder|Invoice whereInvoiceDate($value)
 * @method static Builder|Invoice whereInvoiceDesignId($value)
 * @method static Builder|Invoice whereInvoiceFooter($value)
 * @method static Builder|Invoice whereInvoiceNumber($value)
 * @method static Builder|Invoice whereInvoiceStatusId($value)
 * @method static Builder|Invoice whereInvoiceTypeId($value)
 * @method static Builder|Invoice whereIsAmountDiscount($value)
 * @method static Builder|Invoice whereIsDeleted($value)
 * @method static Builder|Invoice whereIsPublic($value)
 * @method static Builder|Invoice whereIsRecurring($value)
 * @method static Builder|Invoice whereLastSentDate($value)
 * @method static Builder|Invoice wherePartial($value)
 * @method static Builder|Invoice wherePartialDueDate($value)
 * @method static Builder|Invoice wherePoNumber($value)
 * @method static Builder|Invoice wherePrivateNotes($value)
 * @method static Builder|Invoice wherePublicId($value)
 * @method static Builder|Invoice wherePublicNotes($value)
 * @method static Builder|Invoice whereQuoteId($value)
 * @method static Builder|Invoice whereQuoteInvoiceId($value)
 * @method static Builder|Invoice whereRecurringInvoiceId($value)
 * @method static Builder|Invoice whereStartDate($value)
 * @method static Builder|Invoice whereTaxName1($value)
 * @method static Builder|Invoice whereTaxName2($value)
 * @method static Builder|Invoice whereTaxRate1($value)
 * @method static Builder|Invoice whereTaxRate2($value)
 * @method static Builder|Invoice whereTerms($value)
 * @method static Builder|Invoice whereUpdatedAt($value)
 * @method static Builder|Invoice whereUpdatedBy($value)
 * @method static Builder|Invoice whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Invoice withTrashed()
 * @method static Builder|Invoice withoutTrashed()
 * @mixin Eloquent
 */
	class Invoice extends Eloquent implements BalanceAffecting
    {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class InvoiceDesign.
 *
 * @property int $id
 * @property string $name
 * @property string|null $javascript
 * @property string|null $pdfmake
 * @method static Builder|InvoiceDesign newModelQuery()
 * @method static Builder|InvoiceDesign newQuery()
 * @method static Builder|InvoiceDesign query()
 * @method static Builder|InvoiceDesign whereId($value)
 * @method static Builder|InvoiceDesign whereJavascript($value)
 * @method static Builder|InvoiceDesign whereName($value)
 * @method static Builder|InvoiceDesign wherePdfmake($value)
 * @mixin Eloquent
 */
	class InvoiceDesign extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class InvoiceItem.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $product_id
 * @property int|null $warehouse_id
 * @property int|null $invoice_item_type_id
 * @property string|null $product_key
 * @property float|null $cost
 * @property float|null $qty
 * @property float|null $demand_qty
 * @property string|null $tax_name1
 * @property float|null $tax_rate1
 * @property string|null $tax_name2
 * @property float|null $tax_rate2
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property float|null $discount
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Invoice|null $invoice
 * @property-read Product|null $product
 * @property-read User|null $user
 * @method static Builder|InvoiceItem newModelQuery()
 * @method static Builder|InvoiceItem newQuery()
 * @method static Builder|InvoiceItem onlyTrashed()
 * @method static Builder|InvoiceItem query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|InvoiceItem whereAccountId($value)
 * @method static Builder|InvoiceItem whereCost($value)
 * @method static Builder|InvoiceItem whereCreatedAt($value)
 * @method static Builder|InvoiceItem whereCreatedBy($value)
 * @method static Builder|InvoiceItem whereCustomValue1($value)
 * @method static Builder|InvoiceItem whereCustomValue2($value)
 * @method static Builder|InvoiceItem whereDeletedAt($value)
 * @method static Builder|InvoiceItem whereDeletedBy($value)
 * @method static Builder|InvoiceItem whereDemandQty($value)
 * @method static Builder|InvoiceItem whereDiscount($value)
 * @method static Builder|InvoiceItem whereId($value)
 * @method static Builder|InvoiceItem whereInvoiceId($value)
 * @method static Builder|InvoiceItem whereInvoiceItemTypeId($value)
 * @method static Builder|InvoiceItem whereIsDeleted($value)
 * @method static Builder|InvoiceItem whereNotes($value)
 * @method static Builder|InvoiceItem whereProductId($value)
 * @method static Builder|InvoiceItem whereProductKey($value)
 * @method static Builder|InvoiceItem wherePublicId($value)
 * @method static Builder|InvoiceItem whereQty($value)
 * @method static Builder|InvoiceItem whereTaxName1($value)
 * @method static Builder|InvoiceItem whereTaxName2($value)
 * @method static Builder|InvoiceItem whereTaxRate1($value)
 * @method static Builder|InvoiceItem whereTaxRate2($value)
 * @method static Builder|InvoiceItem whereUpdatedAt($value)
 * @method static Builder|InvoiceItem whereUpdatedBy($value)
 * @method static Builder|InvoiceItem whereUserId($value)
 * @method static Builder|InvoiceItem whereWarehouseId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|InvoiceItem withTrashed()
 * @method static Builder|InvoiceItem withoutTrashed()
 * @mixin Eloquent
 */
	class InvoiceItem extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class InvoiceStatus.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|InvoiceStatus newModelQuery()
 * @method static Builder|InvoiceStatus newQuery()
 * @method static Builder|InvoiceStatus query()
 * @method static Builder|InvoiceStatus whereAccountId($value)
 * @method static Builder|InvoiceStatus whereCreatedAt($value)
 * @method static Builder|InvoiceStatus whereCreatedBy($value)
 * @method static Builder|InvoiceStatus whereDeletedAt($value)
 * @method static Builder|InvoiceStatus whereDeletedBy($value)
 * @method static Builder|InvoiceStatus whereId($value)
 * @method static Builder|InvoiceStatus whereIsDeleted($value)
 * @method static Builder|InvoiceStatus whereName($value)
 * @method static Builder|InvoiceStatus whereNotes($value)
 * @method static Builder|InvoiceStatus wherePublicId($value)
 * @method static Builder|InvoiceStatus whereUpdatedAt($value)
 * @method static Builder|InvoiceStatus whereUpdatedBy($value)
 * @method static Builder|InvoiceStatus whereUserId($value)
 * @mixin Eloquent
 */
	class InvoiceStatus extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class ItemBrand.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $item_category_id
 * @property string|null $name
 * @property string|null $notes
 * @property int|null $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read ItemCategory|null $item_category
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read User|null $user
 * @method static Builder|ItemBrand brandWithCategory()
 * @method static Builder|ItemBrand newModelQuery()
 * @method static Builder|ItemBrand newQuery()
 * @method static Builder|ItemBrand onlyTrashed()
 * @method static Builder|ItemBrand query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ItemBrand whereAccountId($value)
 * @method static Builder|ItemBrand whereCreatedAt($value)
 * @method static Builder|ItemBrand whereCreatedBy($value)
 * @method static Builder|ItemBrand whereDeletedAt($value)
 * @method static Builder|ItemBrand whereDeletedBy($value)
 * @method static Builder|ItemBrand whereId($value)
 * @method static Builder|ItemBrand whereIsDeleted($value)
 * @method static Builder|ItemBrand whereItemCategoryId($value)
 * @method static Builder|ItemBrand whereName($value)
 * @method static Builder|ItemBrand whereNotes($value)
 * @method static Builder|ItemBrand wherePublicId($value)
 * @method static Builder|ItemBrand whereUpdatedAt($value)
 * @method static Builder|ItemBrand whereUpdatedBy($value)
 * @method static Builder|ItemBrand whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ItemBrand withTrashed()
 * @method static Builder|ItemBrand withoutTrashed()
 * @mixin Eloquent
 */
	class ItemBrand extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class ItemCategory.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|ItemBrand[] $item_brands
 * @property-read int|null $item_brands_count
 * @property-read User|null $user
 * @method static Builder|ItemCategory newModelQuery()
 * @method static Builder|ItemCategory newQuery()
 * @method static Builder|ItemCategory onlyTrashed()
 * @method static Builder|ItemCategory query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ItemCategory whereAccountId($value)
 * @method static Builder|ItemCategory whereCreatedAt($value)
 * @method static Builder|ItemCategory whereCreatedBy($value)
 * @method static Builder|ItemCategory whereDeletedAt($value)
 * @method static Builder|ItemCategory whereDeletedBy($value)
 * @method static Builder|ItemCategory whereId($value)
 * @method static Builder|ItemCategory whereIsDeleted($value)
 * @method static Builder|ItemCategory whereName($value)
 * @method static Builder|ItemCategory whereNotes($value)
 * @method static Builder|ItemCategory wherePublicId($value)
 * @method static Builder|ItemCategory whereUpdatedAt($value)
 * @method static Builder|ItemCategory whereUpdatedBy($value)
 * @method static Builder|ItemCategory whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ItemCategory withTrashed()
 * @method static Builder|ItemCategory withoutTrashed()
 * @mixin Eloquent
 */
	class ItemCategory extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class ItemPrice.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $client_type_id
 * @property float $unit_price
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Account|null $account
 * @property-read ClientType|null $clientType
 * @property-read Product|null $product
 * @property-read User|null $user
 * @method static Builder|ItemPrice newModelQuery()
 * @method static Builder|ItemPrice newQuery()
 * @method static Builder|ItemPrice onlyTrashed()
 * @method static Builder|ItemPrice query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ItemPrice whereAccountId($value)
 * @method static Builder|ItemPrice whereClientTypeId($value)
 * @method static Builder|ItemPrice whereCreatedAt($value)
 * @method static Builder|ItemPrice whereCreatedBy($value)
 * @method static Builder|ItemPrice whereDeletedAt($value)
 * @method static Builder|ItemPrice whereDeletedBy($value)
 * @method static Builder|ItemPrice whereEndDate($value)
 * @method static Builder|ItemPrice whereId($value)
 * @method static Builder|ItemPrice whereIsDeleted($value)
 * @method static Builder|ItemPrice whereNotes($value)
 * @method static Builder|ItemPrice whereProductId($value)
 * @method static Builder|ItemPrice wherePublicId($value)
 * @method static Builder|ItemPrice whereStartDate($value)
 * @method static Builder|ItemPrice whereUnitPrice($value)
 * @method static Builder|ItemPrice whereUpdatedAt($value)
 * @method static Builder|ItemPrice whereUpdatedBy($value)
 * @method static Builder|ItemPrice whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ItemPrice withTrashed()
 * @method static Builder|ItemPrice withoutTrashed()
 * @mixin Eloquent
 */
	class ItemPrice extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class ItemRequestPresenter.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $department_id
 * @property int|null $warehouse_id
 * @property int|null $status_id
 * @property int|null $qty
 * @property int|null $delivered_qty
 * @property string|null $required_date
 * @property string|null $dispatch_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Department|null $department
 * @property-read Product|null $product
 * @property-read Status|null $status
 * @property-read User|null $user
 * @property-read Warehouse|null $warehouse
 * @method static Builder|ItemRequest newModelQuery()
 * @method static Builder|ItemRequest newQuery()
 * @method static Builder|ItemRequest onlyTrashed()
 * @method static Builder|ItemRequest query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ItemRequest whereAccountId($value)
 * @method static Builder|ItemRequest whereCreatedAt($value)
 * @method static Builder|ItemRequest whereCreatedBy($value)
 * @method static Builder|ItemRequest whereDeletedAt($value)
 * @method static Builder|ItemRequest whereDeletedBy($value)
 * @method static Builder|ItemRequest whereDeliveredQty($value)
 * @method static Builder|ItemRequest whereDepartmentId($value)
 * @method static Builder|ItemRequest whereDispatchDate($value)
 * @method static Builder|ItemRequest whereId($value)
 * @method static Builder|ItemRequest whereIsDeleted($value)
 * @method static Builder|ItemRequest whereNotes($value)
 * @method static Builder|ItemRequest whereProductId($value)
 * @method static Builder|ItemRequest wherePublicId($value)
 * @method static Builder|ItemRequest whereQty($value)
 * @method static Builder|ItemRequest whereRequiredDate($value)
 * @method static Builder|ItemRequest whereStatusId($value)
 * @method static Builder|ItemRequest whereUpdatedAt($value)
 * @method static Builder|ItemRequest whereUpdatedBy($value)
 * @method static Builder|ItemRequest whereUserId($value)
 * @method static Builder|ItemRequest whereWarehouseId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ItemRequest withTrashed()
 * @method static Builder|ItemRequest withoutTrashed()
 * @mixin Eloquent
 */
	class ItemRequest extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class ItemStore.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $warehouse_id
 * @property string|null $bin
 * @property float|null $qty
 * @property int|null $EOQ
 * @property int|null $reorder_level
 * @property int $is_locked
 * @property int $is_public
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Product|null $product
 * @property-read Collection|ItemMovement[] $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read User|null $user
 * @property-read Warehouse|null $warehouse
 * @method static Builder|ItemStore newModelQuery()
 * @method static Builder|ItemStore newQuery()
 * @method static Builder|ItemStore onlyTrashed()
 * @method static Builder|ItemStore query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ItemStore whereAccountId($value)
 * @method static Builder|ItemStore whereBin($value)
 * @method static Builder|ItemStore whereCreatedAt($value)
 * @method static Builder|ItemStore whereCreatedBy($value)
 * @method static Builder|ItemStore whereDeletedAt($value)
 * @method static Builder|ItemStore whereDeletedBy($value)
 * @method static Builder|ItemStore whereEOQ($value)
 * @method static Builder|ItemStore whereId($value)
 * @method static Builder|ItemStore whereIsDeleted($value)
 * @method static Builder|ItemStore whereIsLocked($value)
 * @method static Builder|ItemStore whereIsPublic($value)
 * @method static Builder|ItemStore whereNotes($value)
 * @method static Builder|ItemStore whereProductId($value)
 * @method static Builder|ItemStore wherePublicId($value)
 * @method static Builder|ItemStore whereQty($value)
 * @method static Builder|ItemStore whereReorderLevel($value)
 * @method static Builder|ItemStore whereUpdatedAt($value)
 * @method static Builder|ItemStore whereUpdatedBy($value)
 * @method static Builder|ItemStore whereUserId($value)
 * @method static Builder|ItemStore whereWarehouseId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ItemStore withTrashed()
 * @method static Builder|ItemStore withoutTrashed()
 * @mixin Eloquent
 */
	class ItemStore extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class ItemTransferPresenter.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property int|null $previous_warehouse_id
 * @property int|null $current_warehouse_id
 * @property int|null $approver_id
 * @property int|null $status_id
 * @property int|null $qty
 * @property int $is_deleted
 * @property string|null $notes
 * @property string|null $dispatch_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $approver
 * @property-read Warehouse|null $currentWarehouse
 * @property-read Warehouse|null $previousWarehouse
 * @property-read Product|null $product
 * @property-read Status|null $status
 * @property-read Collection|ItemMovement[] $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read User|null $user
 * @method static Builder|ItemTransfer newModelQuery()
 * @method static Builder|ItemTransfer newQuery()
 * @method static Builder|ItemTransfer onlyTrashed()
 * @method static Builder|ItemTransfer query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ItemTransfer whereAccountId($value)
 * @method static Builder|ItemTransfer whereApproverId($value)
 * @method static Builder|ItemTransfer whereCreatedAt($value)
 * @method static Builder|ItemTransfer whereCreatedBy($value)
 * @method static Builder|ItemTransfer whereCurrentWarehouseId($value)
 * @method static Builder|ItemTransfer whereDeletedAt($value)
 * @method static Builder|ItemTransfer whereDeletedBy($value)
 * @method static Builder|ItemTransfer whereDispatchDate($value)
 * @method static Builder|ItemTransfer whereId($value)
 * @method static Builder|ItemTransfer whereIsDeleted($value)
 * @method static Builder|ItemTransfer whereNotes($value)
 * @method static Builder|ItemTransfer wherePreviousWarehouseId($value)
 * @method static Builder|ItemTransfer whereProductId($value)
 * @method static Builder|ItemTransfer wherePublicId($value)
 * @method static Builder|ItemTransfer whereQty($value)
 * @method static Builder|ItemTransfer whereStatusId($value)
 * @method static Builder|ItemTransfer whereUpdatedAt($value)
 * @method static Builder|ItemTransfer whereUpdatedBy($value)
 * @method static Builder|ItemTransfer whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ItemTransfer withTrashed()
 * @method static Builder|ItemTransfer withoutTrashed()
 * @mixin Eloquent
 */
	class ItemTransfer extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ItemType.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int|null $user_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @method static Builder|ItemType newModelQuery()
 * @method static Builder|ItemType newQuery()
 * @method static Builder|ItemType query()
 * @method static Builder|ItemType whereAccountId($value)
 * @method static Builder|ItemType whereCreatedAt($value)
 * @method static Builder|ItemType whereDeletedAt($value)
 * @method static Builder|ItemType whereId($value)
 * @method static Builder|ItemType whereName($value)
 * @method static Builder|ItemType wherePublicId($value)
 * @method static Builder|ItemType whereUpdatedAt($value)
 * @method static Builder|ItemType whereUserId($value)
 * @mixin Eloquent
 */
	class ItemType extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Language.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $locale
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereCreatedAt($value)
 * @method static Builder|Language whereCreatedBy($value)
 * @method static Builder|Language whereDeletedAt($value)
 * @method static Builder|Language whereDeletedBy($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereLocale($value)
 * @method static Builder|Language whereName($value)
 * @method static Builder|Language whereUpdatedAt($value)
 * @method static Builder|Language whereUpdatedBy($value)
 * @mixin Eloquent
 */
	class Language extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Model Class License.
 *
 * @property int $id
 * @property int|null $affiliate_id
 * @property int|null $product_id
 * @property string|null $license_key
 * @property string|null $transaction_reference
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property int|null $is_claimed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|License newModelQuery()
 * @method static Builder|License newQuery()
 * @method static Builder|License onlyTrashed()
 * @method static Builder|License query()
 * @method static Builder|License whereAffiliateId($value)
 * @method static Builder|License whereCreatedAt($value)
 * @method static Builder|License whereCreatedBy($value)
 * @method static Builder|License whereDeletedAt($value)
 * @method static Builder|License whereDeletedBy($value)
 * @method static Builder|License whereEmail($value)
 * @method static Builder|License whereFirstName($value)
 * @method static Builder|License whereId($value)
 * @method static Builder|License whereIsClaimed($value)
 * @method static Builder|License whereLastName($value)
 * @method static Builder|License whereLicenseKey($value)
 * @method static Builder|License whereProductId($value)
 * @method static Builder|License whereTransactionReference($value)
 * @method static Builder|License whereUpdatedAt($value)
 * @method static Builder|License whereUpdatedBy($value)
 * @method static Builder|License withTrashed()
 * @method static Builder|License withoutTrashed()
 * @mixin Eloquent
 */
	class License extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_company_id
 * @property string|null $account_key
 * @property string|null $subdomain
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount $lookupAccount
 * @property-read LookupCompany|null $lookupCompany
 * @method static Builder|LookupAccount newModelQuery()
 * @method static Builder|LookupAccount newQuery()
 * @method static Builder|LookupAccount query()
 * @method static Builder|LookupAccount whereAccountKey($value)
 * @method static Builder|LookupAccount whereCreatedAt($value)
 * @method static Builder|LookupAccount whereDeletedAt($value)
 * @method static Builder|LookupAccount whereId($value)
 * @method static Builder|LookupAccount whereLookupCompanyId($value)
 * @method static Builder|LookupAccount whereSubdomain($value)
 * @method static Builder|LookupAccount whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class LookupAccount extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $token
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount|null $lookupAccount
 * @method static Builder|LookupAccountToken newModelQuery()
 * @method static Builder|LookupAccountToken newQuery()
 * @method static Builder|LookupAccountToken query()
 * @method static Builder|LookupAccountToken whereCreatedAt($value)
 * @method static Builder|LookupAccountToken whereDeletedAt($value)
 * @method static Builder|LookupAccountToken whereId($value)
 * @method static Builder|LookupAccountToken whereLookupAccountId($value)
 * @method static Builder|LookupAccountToken whereToken($value)
 * @method static Builder|LookupAccountToken whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class LookupAccountToken extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ExpenseCategory.
 *
 * @property-read LookupAccount $lookupAccount
 * @method static Builder|LookupBillInvitation newModelQuery()
 * @method static Builder|LookupBillInvitation newQuery()
 * @method static Builder|LookupBillInvitation query()
 */
	class LookupBillInvitation extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $db_server_id
 * @property int|null $company_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read DbServer|null $dbServer
 * @property-read LookupAccount $lookupAccount
 * @method static Builder|LookupCompany newModelQuery()
 * @method static Builder|LookupCompany newQuery()
 * @method static Builder|LookupCompany query()
 * @method static Builder|LookupCompany whereCompanyId($value)
 * @method static Builder|LookupCompany whereCreatedAt($value)
 * @method static Builder|LookupCompany whereDbServerId($value)
 * @method static Builder|LookupCompany whereDeletedAt($value)
 * @method static Builder|LookupCompany whereId($value)
 * @method static Builder|LookupCompany whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class LookupCompany extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $contact_key
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount|null $lookupAccount
 * @method static Builder|LookupContact newModelQuery()
 * @method static Builder|LookupContact newQuery()
 * @method static Builder|LookupContact query()
 * @method static Builder|LookupContact whereContactKey($value)
 * @method static Builder|LookupContact whereCreatedAt($value)
 * @method static Builder|LookupContact whereDeletedAt($value)
 * @method static Builder|LookupContact whereId($value)
 * @method static Builder|LookupContact whereLookupAccountId($value)
 * @method static Builder|LookupContact whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class LookupContact extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class LookupInvitation.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $invitation_key
 * @property string|null $message_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read LookupAccount|null $lookupAccount
 * @method static Builder|LookupInvitation newModelQuery()
 * @method static Builder|LookupInvitation newQuery()
 * @method static Builder|LookupInvitation query()
 * @method static Builder|LookupInvitation whereCreatedAt($value)
 * @method static Builder|LookupInvitation whereDeletedAt($value)
 * @method static Builder|LookupInvitation whereId($value)
 * @method static Builder|LookupInvitation whereInvitationKey($value)
 * @method static Builder|LookupInvitation whereLookupAccountId($value)
 * @method static Builder|LookupInvitation whereMessageId($value)
 * @method static Builder|LookupInvitation whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class LookupInvitation extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @property-read LookupAccount $lookupAccount
 * @method static Builder|LookupModel newModelQuery()
 * @method static Builder|LookupModel newQuery()
 * @method static Builder|LookupModel query()
 * @mixin Eloquent
 */
	class LookupModel extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property string|null $invitation_key
 * @property string|null $message_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $delete_at
 * @method static Builder|LookupProposalInvitation newModelQuery()
 * @method static Builder|LookupProposalInvitation newQuery()
 * @method static Builder|LookupProposalInvitation query()
 * @method static Builder|LookupProposalInvitation whereCreatedAt($value)
 * @method static Builder|LookupProposalInvitation whereDeleteAt($value)
 * @method static Builder|LookupProposalInvitation whereId($value)
 * @method static Builder|LookupProposalInvitation whereInvitationKey($value)
 * @method static Builder|LookupProposalInvitation whereLookupAccountId($value)
 * @method static Builder|LookupProposalInvitation whereMessageId($value)
 * @method static Builder|LookupProposalInvitation whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read LookupAccount|null $lookupAccount
 */
	class LookupProposalInvitation extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class LookupUser.
 *
 * @property int $id
 * @property int|null $lookup_account_id
 * @property int|null $user_id
 * @property string|null $email
 * @property string|null $confirmation_code
 * @property string|null $oauth_user_key
 * @property string|null $referral_code
 * @property string|null $create_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|LookupUser newModelQuery()
 * @method static Builder|LookupUser newQuery()
 * @method static Builder|LookupUser query()
 * @method static Builder|LookupUser whereConfirmationCode($value)
 * @method static Builder|LookupUser whereCreateAt($value)
 * @method static Builder|LookupUser whereDeletedAt($value)
 * @method static Builder|LookupUser whereEmail($value)
 * @method static Builder|LookupUser whereId($value)
 * @method static Builder|LookupUser whereLookupAccountId($value)
 * @method static Builder|LookupUser whereOauthUserKey($value)
 * @method static Builder|LookupUser whereReferralCode($value)
 * @method static Builder|LookupUser whereUpdatedAt($value)
 * @method static Builder|LookupUser whereUserId($value)
 * @mixin Eloquent
 * @property-read LookupAccount|null $lookupAccount
 */
	class LookupUser extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * App\Models\Manufacturer
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|ManufacturerProductDetails[] $manufacturerProductDetails
 * @property-read int|null $manufacturer_product_details_count
 * @property-read User|null $user
 * @method static Builder|Manufacturer newModelQuery()
 * @method static Builder|Manufacturer newQuery()
 * @method static Builder|Manufacturer onlyTrashed()
 * @method static Builder|Manufacturer query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Manufacturer whereAccountId($value)
 * @method static Builder|Manufacturer whereClientId($value)
 * @method static Builder|Manufacturer whereCreatedAt($value)
 * @method static Builder|Manufacturer whereCreatedBy($value)
 * @method static Builder|Manufacturer whereDeletedAt($value)
 * @method static Builder|Manufacturer whereDeletedBy($value)
 * @method static Builder|Manufacturer whereId($value)
 * @method static Builder|Manufacturer whereIsDeleted($value)
 * @method static Builder|Manufacturer whereName($value)
 * @method static Builder|Manufacturer whereNotes($value)
 * @method static Builder|Manufacturer wherePublicId($value)
 * @method static Builder|Manufacturer whereUpdatedAt($value)
 * @method static Builder|Manufacturer whereUpdatedBy($value)
 * @method static Builder|Manufacturer whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Manufacturer withTrashed()
 * @method static Builder|Manufacturer withoutTrashed()
 * @mixin Eloquent
 */
	class Manufacturer extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Client.
 *
 * @property string $email
 * @property string|null $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|PasswordReset newModelQuery()
 * @method static Builder|PasswordReset newQuery()
 * @method static Builder|PasswordReset query()
 * @method static Builder|PasswordReset whereCreatedAt($value)
 * @method static Builder|PasswordReset whereDeletedAt($value)
 * @method static Builder|PasswordReset whereEmail($value)
 * @method static Builder|PasswordReset whereToken($value)
 * @method static Builder|PasswordReset whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class PasswordReset extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * App\Models\Payment
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $invoice_id
 * @property int|null $client_id
 * @property int|null $contact_id
 * @property int|null $account_gateway_id
 * @property int|null $payment_type_id
 * @property int|null $user_id
 * @property int|null $payment_status_id
 * @property int|null $payment_method_id
 * @property int|null $exchange_currency_id
 * @property int|null $invitation_id
 * @property string|null $payer_id
 * @property float $amount
 * @property float $refunded
 * @property string|null $payment_date
 * @property string|null $transaction_reference
 * @property int|null $routing_number
 * @property int|null $last4
 * @property string|null $expiration
 * @property string|null $gateway_error
 * @property string|null $email
 * @property string|null $bank_name
 * @property string|null $ip
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property string|null $credit_ids
 * @property string|null $private_notes
 * @property string|null $public_notes
 * @property float $exchange_rate
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Account|null $account
 * @property-read AccountGateway|null $account_gateway
 * @property-read Client|null $client
 * @property-read Contact|null $contact
 * @property-read mixed $bank_data
 * @property-read Invitation|null $invitation
 * @property-read Invoice|null $invoice
 * @property-read PaymentMethod|null $payment_method
 * @property-read PaymentStatus|null $payment_status
 * @property-read PaymentType|null $payment_type
 * @property-read User|null $user
 * @method static Builder|Payment dateRange($startDate, $endDate)
 * @method static Builder|Payment excludeFailed()
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment onlyTrashed()
 * @method static Builder|Payment query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Payment whereAccountGatewayId($value)
 * @method static Builder|Payment whereAccountId($value)
 * @method static Builder|Payment whereAmount($value)
 * @method static Builder|Payment whereBankName($value)
 * @method static Builder|Payment whereClientId($value)
 * @method static Builder|Payment whereContactId($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereCreatedBy($value)
 * @method static Builder|Payment whereCreditIds($value)
 * @method static Builder|Payment whereDeletedAt($value)
 * @method static Builder|Payment whereDeletedBy($value)
 * @method static Builder|Payment whereEmail($value)
 * @method static Builder|Payment whereExchangeCurrencyId($value)
 * @method static Builder|Payment whereExchangeRate($value)
 * @method static Builder|Payment whereExpiration($value)
 * @method static Builder|Payment whereGatewayError($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment whereInvitationId($value)
 * @method static Builder|Payment whereInvoiceId($value)
 * @method static Builder|Payment whereIp($value)
 * @method static Builder|Payment whereIsDeleted($value)
 * @method static Builder|Payment whereLast4($value)
 * @method static Builder|Payment wherePayerId($value)
 * @method static Builder|Payment wherePaymentDate($value)
 * @method static Builder|Payment wherePaymentMethodId($value)
 * @method static Builder|Payment wherePaymentStatusId($value)
 * @method static Builder|Payment wherePaymentTypeId($value)
 * @method static Builder|Payment wherePrivateNotes($value)
 * @method static Builder|Payment wherePublicId($value)
 * @method static Builder|Payment wherePublicNotes($value)
 * @method static Builder|Payment whereRefunded($value)
 * @method static Builder|Payment whereRoutingNumber($value)
 * @method static Builder|Payment whereTransactionReference($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @method static Builder|Payment whereUpdatedBy($value)
 * @method static Builder|Payment whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Payment withTrashed()
 * @method static Builder|Payment withoutTrashed()
 * @mixin Eloquent
 */
	class Payment extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class PaymentLibrary.
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $visible
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Gateway[] $gateways
 * @property-read int|null $gateways_count
 * @method static Builder|PaymentLibrary newModelQuery()
 * @method static Builder|PaymentLibrary newQuery()
 * @method static Builder|PaymentLibrary query()
 * @method static Builder|PaymentLibrary whereCreatedAt($value)
 * @method static Builder|PaymentLibrary whereCreatedBy($value)
 * @method static Builder|PaymentLibrary whereDeletedAt($value)
 * @method static Builder|PaymentLibrary whereDeletedBy($value)
 * @method static Builder|PaymentLibrary whereId($value)
 * @method static Builder|PaymentLibrary whereName($value)
 * @method static Builder|PaymentLibrary whereUpdatedAt($value)
 * @method static Builder|PaymentLibrary whereUpdatedBy($value)
 * @method static Builder|PaymentLibrary whereVisible($value)
 * @mixin Eloquent
 */
	class PaymentLibrary extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class PaymentStatus.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|PaymentStatus newModelQuery()
 * @method static Builder|PaymentStatus newQuery()
 * @method static Builder|PaymentStatus query()
 * @method static Builder|PaymentStatus whereAccountId($value)
 * @method static Builder|PaymentStatus whereCreatedAt($value)
 * @method static Builder|PaymentStatus whereCreatedBy($value)
 * @method static Builder|PaymentStatus whereDeletedAt($value)
 * @method static Builder|PaymentStatus whereDeletedBy($value)
 * @method static Builder|PaymentStatus whereId($value)
 * @method static Builder|PaymentStatus whereName($value)
 * @method static Builder|PaymentStatus wherePublicId($value)
 * @method static Builder|PaymentStatus whereUpdatedAt($value)
 * @method static Builder|PaymentStatus whereUpdatedBy($value)
 * @method static Builder|PaymentStatus whereUserId($value)
 * @mixin Eloquent
 */
	class PaymentStatus extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class PaymentTerm.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int $num_days
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|PaymentTerm newModelQuery()
 * @method static Builder|PaymentTerm newQuery()
 * @method static Builder|PaymentTerm onlyTrashed()
 * @method static Builder|PaymentTerm query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|PaymentTerm whereAccountId($value)
 * @method static Builder|PaymentTerm whereCreatedAt($value)
 * @method static Builder|PaymentTerm whereDeletedAt($value)
 * @method static Builder|PaymentTerm whereId($value)
 * @method static Builder|PaymentTerm whereName($value)
 * @method static Builder|PaymentTerm whereNumDays($value)
 * @method static Builder|PaymentTerm wherePublicId($value)
 * @method static Builder|PaymentTerm whereUpdatedAt($value)
 * @method static Builder|PaymentTerm whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|PaymentTerm withTrashed()
 * @method static Builder|PaymentTerm withoutTrashed()
 * @mixin Eloquent
 */
	class PaymentTerm extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class PaymentType.
 *
 * @property int $id
 * @property string $name
 * @property int|null $gateway_type_id
 * @property-read GatewayType|null $gatewayType
 * @method static Builder|PaymentType newModelQuery()
 * @method static Builder|PaymentType newQuery()
 * @method static Builder|PaymentType paymentTypes()
 * @method static Builder|PaymentType query()
 * @method static Builder|PaymentType whereGatewayTypeId($value)
 * @method static Builder|PaymentType whereId($value)
 * @method static Builder|PaymentType whereName($value)
 * @mixin Eloquent
 */
	class PaymentType extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Model Permission.
 *
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission onlyTrashed()
 * @method static Builder|Permission query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Permission withTrashed()
 * @method static Builder|Permission withoutTrashed()
 * @mixin Eloquent
 */
	class Permission extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Model PermissionGroup.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $name
 * @property string|null $permissions
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|PermissionGroup newModelQuery()
 * @method static Builder|PermissionGroup newQuery()
 * @method static Builder|PermissionGroup onlyTrashed()
 * @method static Builder|PermissionGroup query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|PermissionGroup whereAccountId($value)
 * @method static Builder|PermissionGroup whereCreatedAt($value)
 * @method static Builder|PermissionGroup whereCreatedBy($value)
 * @method static Builder|PermissionGroup whereDeletedAt($value)
 * @method static Builder|PermissionGroup whereDeletedBy($value)
 * @method static Builder|PermissionGroup whereId($value)
 * @method static Builder|PermissionGroup whereIsDeleted($value)
 * @method static Builder|PermissionGroup whereName($value)
 * @method static Builder|PermissionGroup whereNotes($value)
 * @method static Builder|PermissionGroup wherePermissions($value)
 * @method static Builder|PermissionGroup wherePublicId($value)
 * @method static Builder|PermissionGroup whereUpdatedAt($value)
 * @method static Builder|PermissionGroup whereUpdatedBy($value)
 * @method static Builder|PermissionGroup whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|PermissionGroup withTrashed()
 * @method static Builder|PermissionGroup withoutTrashed()
 * @mixin Eloquent
 */
	class PermissionGroup extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class Category.
 *
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Plan newModelQuery()
 * @method static Builder|Plan newQuery()
 * @method static Builder|Plan query()
 * @method static Builder|Plan whereCreatedAt($value)
 * @method static Builder|Plan whereDeletedAt($value)
 * @method static Builder|Plan whereId($value)
 * @method static Builder|Plan whereName($value)
 * @method static Builder|Plan whereUpdatedAt($value)
 * @mixin Eloquent
 */
	class Plan extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $account_id
 * @property int|null $client_id
 * @property int|null $public_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $name
 * @property float $task_rate
 * @property string|null $due_date
 * @property int $is_deleted
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property string|null $private_notes
 * @property float $budgeted_hours
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read Collection|Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static Builder|Project dateRange($startDate, $endDate)
 * @method static Builder|Project newModelQuery()
 * @method static Builder|Project newQuery()
 * @method static Builder|Project onlyTrashed()
 * @method static Builder|Project query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Project whereAccountId($value)
 * @method static Builder|Project whereBudgetedHours($value)
 * @method static Builder|Project whereClientId($value)
 * @method static Builder|Project whereCreatedAt($value)
 * @method static Builder|Project whereCreatedBy($value)
 * @method static Builder|Project whereCustomValue1($value)
 * @method static Builder|Project whereCustomValue2($value)
 * @method static Builder|Project whereDeletedAt($value)
 * @method static Builder|Project whereDeletedBy($value)
 * @method static Builder|Project whereDueDate($value)
 * @method static Builder|Project whereId($value)
 * @method static Builder|Project whereIsDeleted($value)
 * @method static Builder|Project whereName($value)
 * @method static Builder|Project wherePrivateNotes($value)
 * @method static Builder|Project wherePublicId($value)
 * @method static Builder|Project whereTaskRate($value)
 * @method static Builder|Project whereUpdatedAt($value)
 * @method static Builder|Project whereUpdatedBy($value)
 * @method static Builder|Project whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Project withTrashed()
 * @method static Builder|Project withoutTrashed()
 * @mixin Eloquent
 */
	class Project extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $proposal_template_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $private_notes
 * @property string|null $html
 * @property string|null $css
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|ProposalInvitation[] $invitations
 * @property-read int|null $invitations_count
 * @property-read Invoice|null $invoice
 * @property-read ProposalTemplate|null $proposal_template
 * @method static Builder|Proposal newModelQuery()
 * @method static Builder|Proposal newQuery()
 * @method static Builder|Proposal onlyTrashed()
 * @method static Builder|Proposal query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Proposal whereAccountId($value)
 * @method static Builder|Proposal whereCreatedAt($value)
 * @method static Builder|Proposal whereCreatedBy($value)
 * @method static Builder|Proposal whereCss($value)
 * @method static Builder|Proposal whereDeletedAt($value)
 * @method static Builder|Proposal whereDeletedBy($value)
 * @method static Builder|Proposal whereHtml($value)
 * @method static Builder|Proposal whereId($value)
 * @method static Builder|Proposal whereInvoiceId($value)
 * @method static Builder|Proposal whereIsDeleted($value)
 * @method static Builder|Proposal wherePrivateNotes($value)
 * @method static Builder|Proposal whereProposalTemplateId($value)
 * @method static Builder|Proposal wherePublicId($value)
 * @method static Builder|Proposal whereUpdatedAt($value)
 * @method static Builder|Proposal whereUpdatedBy($value)
 * @method static Builder|Proposal whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|Proposal withTrashed()
 * @method static Builder|Proposal withoutTrashed()
 * @mixin Eloquent
 */
	class Proposal extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $name
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @method static Builder|ProposalCategory newModelQuery()
 * @method static Builder|ProposalCategory newQuery()
 * @method static Builder|ProposalCategory onlyTrashed()
 * @method static Builder|ProposalCategory query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ProposalCategory whereAccountId($value)
 * @method static Builder|ProposalCategory whereCreatedAt($value)
 * @method static Builder|ProposalCategory whereCreatedBy($value)
 * @method static Builder|ProposalCategory whereDeletedAt($value)
 * @method static Builder|ProposalCategory whereDeletedBy($value)
 * @method static Builder|ProposalCategory whereId($value)
 * @method static Builder|ProposalCategory whereIsDeleted($value)
 * @method static Builder|ProposalCategory whereName($value)
 * @method static Builder|ProposalCategory wherePublicId($value)
 * @method static Builder|ProposalCategory whereUpdatedAt($value)
 * @method static Builder|ProposalCategory whereUpdatedBy($value)
 * @method static Builder|ProposalCategory whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ProposalCategory withTrashed()
 * @method static Builder|ProposalCategory withoutTrashed()
 * @mixin Eloquent
 */
	class ProposalCategory extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ProposalInvitation.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property int|null $proposal_id
 * @property string|null $invitation_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $sent_date
 * @property string|null $viewed_date
 * @property string|null $opened_date
 * @property string|null $message_id
 * @property string|null $email_error
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Contact|null $contact
 * @property-read Proposal|null $proposal
 * @property-read User|null $user
 * @method static Builder|ProposalInvitation newModelQuery()
 * @method static Builder|ProposalInvitation newQuery()
 * @method static Builder|ProposalInvitation onlyTrashed()
 * @method static Builder|ProposalInvitation query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ProposalInvitation whereAccountId($value)
 * @method static Builder|ProposalInvitation whereContactId($value)
 * @method static Builder|ProposalInvitation whereCreatedAt($value)
 * @method static Builder|ProposalInvitation whereCreatedBy($value)
 * @method static Builder|ProposalInvitation whereDeletedAt($value)
 * @method static Builder|ProposalInvitation whereDeletedBy($value)
 * @method static Builder|ProposalInvitation whereEmailError($value)
 * @method static Builder|ProposalInvitation whereId($value)
 * @method static Builder|ProposalInvitation whereInvitationKey($value)
 * @method static Builder|ProposalInvitation whereMessageId($value)
 * @method static Builder|ProposalInvitation whereOpenedDate($value)
 * @method static Builder|ProposalInvitation whereProposalId($value)
 * @method static Builder|ProposalInvitation wherePublicId($value)
 * @method static Builder|ProposalInvitation whereSentDate($value)
 * @method static Builder|ProposalInvitation whereUpdatedAt($value)
 * @method static Builder|ProposalInvitation whereUpdatedBy($value)
 * @method static Builder|ProposalInvitation whereUserId($value)
 * @method static Builder|ProposalInvitation whereViewedDate($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ProposalInvitation withTrashed()
 * @method static Builder|ProposalInvitation withoutTrashed()
 * @mixin Eloquent
 */
	class ProposalInvitation extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property int|null $proposal_category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $name
 * @property string|null $icon
 * @property string|null $private_notes
 * @property string|null $html
 * @property string|null $css
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read ProposalCategory|null $proposal_category
 * @method static Builder|ProposalSnippet newModelQuery()
 * @method static Builder|ProposalSnippet newQuery()
 * @method static Builder|ProposalSnippet onlyTrashed()
 * @method static Builder|ProposalSnippet query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ProposalSnippet whereAccountId($value)
 * @method static Builder|ProposalSnippet whereCreatedAt($value)
 * @method static Builder|ProposalSnippet whereCreatedBy($value)
 * @method static Builder|ProposalSnippet whereCss($value)
 * @method static Builder|ProposalSnippet whereDeletedAt($value)
 * @method static Builder|ProposalSnippet whereDeletedBy($value)
 * @method static Builder|ProposalSnippet whereHtml($value)
 * @method static Builder|ProposalSnippet whereIcon($value)
 * @method static Builder|ProposalSnippet whereId($value)
 * @method static Builder|ProposalSnippet whereIsDeleted($value)
 * @method static Builder|ProposalSnippet whereName($value)
 * @method static Builder|ProposalSnippet wherePrivateNotes($value)
 * @method static Builder|ProposalSnippet whereProposalCategoryId($value)
 * @method static Builder|ProposalSnippet wherePublicId($value)
 * @method static Builder|ProposalSnippet whereUpdatedAt($value)
 * @method static Builder|ProposalSnippet whereUpdatedBy($value)
 * @method static Builder|ProposalSnippet whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ProposalSnippet withTrashed()
 * @method static Builder|ProposalSnippet withoutTrashed()
 * @mixin Eloquent
 */
	class ProposalSnippet extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ExpenseCategory.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_deleted
 * @property string|null $private_notes
 * @property string|null $name
 * @property string|null $html
 * @property string|null $css
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @method static Builder|ProposalTemplate newModelQuery()
 * @method static Builder|ProposalTemplate newQuery()
 * @method static Builder|ProposalTemplate onlyTrashed()
 * @method static Builder|ProposalTemplate query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ProposalTemplate whereAccountId($value)
 * @method static Builder|ProposalTemplate whereCreatedAt($value)
 * @method static Builder|ProposalTemplate whereCreatedBy($value)
 * @method static Builder|ProposalTemplate whereCss($value)
 * @method static Builder|ProposalTemplate whereDeletedAt($value)
 * @method static Builder|ProposalTemplate whereDeletedBy($value)
 * @method static Builder|ProposalTemplate whereHtml($value)
 * @method static Builder|ProposalTemplate whereId($value)
 * @method static Builder|ProposalTemplate whereIsDeleted($value)
 * @method static Builder|ProposalTemplate whereName($value)
 * @method static Builder|ProposalTemplate wherePrivateNotes($value)
 * @method static Builder|ProposalTemplate wherePublicId($value)
 * @method static Builder|ProposalTemplate whereUpdatedAt($value)
 * @method static Builder|ProposalTemplate whereUpdatedBy($value)
 * @method static Builder|ProposalTemplate whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ProposalTemplate withTrashed()
 * @method static Builder|ProposalTemplate withoutTrashed()
 * @mixin Eloquent
 */
	class ProposalTemplate extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class ExpenseCategory.
 *
 * @method static Builder|Quote newModelQuery()
 * @method static Builder|Quote newQuery()
 * @method static Builder|Quote query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
	class Quote extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Expense.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_currency_id
 * @property int|null $expense_currency_id
 * @property int|null $expense_category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $vendor_id
 * @property int|null $client_id
 * @property int $is_deleted
 * @property float $amount
 * @property string $private_notes
 * @property string $public_notes
 * @property int $should_be_invoiced
 * @property string|null $tax_name1
 * @property float $tax_rate1
 * @property string|null $tax_name2
 * @property float $tax_rate2
 * @property int $frequency_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $last_sent_date
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read ExpenseCategory|null $expense_category
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @method static Builder|RecurringExpense newModelQuery()
 * @method static Builder|RecurringExpense newQuery()
 * @method static Builder|RecurringExpense onlyTrashed()
 * @method static Builder|RecurringExpense query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|RecurringExpense whereAccountId($value)
 * @method static Builder|RecurringExpense whereAmount($value)
 * @method static Builder|RecurringExpense whereClientId($value)
 * @method static Builder|RecurringExpense whereCreatedAt($value)
 * @method static Builder|RecurringExpense whereCreatedBy($value)
 * @method static Builder|RecurringExpense whereDeletedAt($value)
 * @method static Builder|RecurringExpense whereDeletedBy($value)
 * @method static Builder|RecurringExpense whereEndDate($value)
 * @method static Builder|RecurringExpense whereExpenseCategoryId($value)
 * @method static Builder|RecurringExpense whereExpenseCurrencyId($value)
 * @method static Builder|RecurringExpense whereFrequencyId($value)
 * @method static Builder|RecurringExpense whereId($value)
 * @method static Builder|RecurringExpense whereInvoiceCurrencyId($value)
 * @method static Builder|RecurringExpense whereIsDeleted($value)
 * @method static Builder|RecurringExpense whereLastSentDate($value)
 * @method static Builder|RecurringExpense wherePrivateNotes($value)
 * @method static Builder|RecurringExpense wherePublicId($value)
 * @method static Builder|RecurringExpense wherePublicNotes($value)
 * @method static Builder|RecurringExpense whereShouldBeInvoiced($value)
 * @method static Builder|RecurringExpense whereStartDate($value)
 * @method static Builder|RecurringExpense whereTaxName1($value)
 * @method static Builder|RecurringExpense whereTaxName2($value)
 * @method static Builder|RecurringExpense whereTaxRate1($value)
 * @method static Builder|RecurringExpense whereTaxRate2($value)
 * @method static Builder|RecurringExpense whereUpdatedAt($value)
 * @method static Builder|RecurringExpense whereUpdatedBy($value)
 * @method static Builder|RecurringExpense whereUserId($value)
 * @method static Builder|RecurringExpense whereVendorId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|RecurringExpense withTrashed()
 * @method static Builder|RecurringExpense withoutTrashed()
 * @mixin Eloquent
 */
	class RecurringExpense extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ScheduleCategory.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property int|null $is_deleted
 * @property string|null $name
 * @property string|null $text_color
 * @property string|null $bg_color
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|ScheduleCategory newModelQuery()
 * @method static Builder|ScheduleCategory newQuery()
 * @method static Builder|ScheduleCategory onlyTrashed()
 * @method static Builder|ScheduleCategory query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ScheduleCategory whereAccountId($value)
 * @method static Builder|ScheduleCategory whereBgColor($value)
 * @method static Builder|ScheduleCategory whereCreatedAt($value)
 * @method static Builder|ScheduleCategory whereCreatedBy($value)
 * @method static Builder|ScheduleCategory whereDeletedAt($value)
 * @method static Builder|ScheduleCategory whereDeletedBy($value)
 * @method static Builder|ScheduleCategory whereId($value)
 * @method static Builder|ScheduleCategory whereIsDeleted($value)
 * @method static Builder|ScheduleCategory whereName($value)
 * @method static Builder|ScheduleCategory whereNotes($value)
 * @method static Builder|ScheduleCategory wherePublicId($value)
 * @method static Builder|ScheduleCategory whereTextColor($value)
 * @method static Builder|ScheduleCategory whereUpdatedAt($value)
 * @method static Builder|ScheduleCategory whereUpdatedBy($value)
 * @method static Builder|ScheduleCategory whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ScheduleCategory withTrashed()
 * @method static Builder|ScheduleCategory withoutTrashed()
 * @mixin Eloquent
 */
	class ScheduleCategory extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class Scheduled Report
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $ip
 * @property string|null $frequency
 * @property string|null $config
 * @property string|null $send_date
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $user
 * @method static Builder|ScheduledReport newModelQuery()
 * @method static Builder|ScheduledReport newQuery()
 * @method static Builder|ScheduledReport onlyTrashed()
 * @method static Builder|ScheduledReport query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ScheduledReport whereAccountId($value)
 * @method static Builder|ScheduledReport whereConfig($value)
 * @method static Builder|ScheduledReport whereCreatedAt($value)
 * @method static Builder|ScheduledReport whereCreatedBy($value)
 * @method static Builder|ScheduledReport whereDeletedAt($value)
 * @method static Builder|ScheduledReport whereDeletedBy($value)
 * @method static Builder|ScheduledReport whereFrequency($value)
 * @method static Builder|ScheduledReport whereId($value)
 * @method static Builder|ScheduledReport whereIp($value)
 * @method static Builder|ScheduledReport whereIsDeleted($value)
 * @method static Builder|ScheduledReport wherePublicId($value)
 * @method static Builder|ScheduledReport whereSendDate($value)
 * @method static Builder|ScheduledReport whereUpdatedAt($value)
 * @method static Builder|ScheduledReport whereUpdatedBy($value)
 * @method static Builder|ScheduledReport whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ScheduledReport withTrashed()
 * @method static Builder|ScheduledReport withoutTrashed()
 * @mixin Eloquent
 */
	class ScheduledReport extends Eloquent {}
}

namespace App\Models{

    use Eloquent;

    /**
 * Class DatetimeFormat.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property string|null $bot_user_id
 * @property int $attempts
 * @property string|null $code
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|SecurityCode newModelQuery()
 * @method static Builder|SecurityCode newQuery()
 * @method static Builder|SecurityCode query()
 * @method static Builder|SecurityCode whereAccountId($value)
 * @method static Builder|SecurityCode whereAttempts($value)
 * @method static Builder|SecurityCode whereBotUserId($value)
 * @method static Builder|SecurityCode whereCode($value)
 * @method static Builder|SecurityCode whereContactId($value)
 * @method static Builder|SecurityCode whereCreatedAt($value)
 * @method static Builder|SecurityCode whereCreatedBy($value)
 * @method static Builder|SecurityCode whereDeletedAt($value)
 * @method static Builder|SecurityCode whereDeletedBy($value)
 * @method static Builder|SecurityCode whereId($value)
 * @method static Builder|SecurityCode whereUpdatedAt($value)
 * @method static Builder|SecurityCode whereUpdatedBy($value)
 * @method static Builder|SecurityCode whereUserId($value)
 * @mixin Eloquent
 */
	class SecurityCode extends Eloquent {}
}

namespace App\Models\Setting{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class ClientType.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read Collection|ItemPrice[] $itemPrices
 * @property-read int|null $item_prices_count
 * @method static Builder|ClientType newModelQuery()
 * @method static Builder|ClientType newQuery()
 * @method static Builder|ClientType onlyTrashed()
 * @method static Builder|ClientType query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|ClientType whereAccountId($value)
 * @method static Builder|ClientType whereCreatedAt($value)
 * @method static Builder|ClientType whereCreatedBy($value)
 * @method static Builder|ClientType whereDeletedAt($value)
 * @method static Builder|ClientType whereDeletedBy($value)
 * @method static Builder|ClientType whereId($value)
 * @method static Builder|ClientType whereIsDeleted($value)
 * @method static Builder|ClientType whereName($value)
 * @method static Builder|ClientType whereNotes($value)
 * @method static Builder|ClientType wherePublicId($value)
 * @method static Builder|ClientType whereUpdatedAt($value)
 * @method static Builder|ClientType whereUpdatedBy($value)
 * @method static Builder|ClientType whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|ClientType withTrashed()
 * @method static Builder|ClientType withoutTrashed()
 * @mixin Eloquent
 */
	class ClientType extends Eloquent {}
}

namespace App\Models\Setting{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Product.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read Collection|ItemPrice[] $itemPrices
 * @property-read int|null $item_prices_count
 * @method static Builder|SaleType newModelQuery()
 * @method static Builder|SaleType newQuery()
 * @method static Builder|SaleType onlyTrashed()
 * @method static Builder|SaleType query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|SaleType whereAccountId($value)
 * @method static Builder|SaleType whereCreatedAt($value)
 * @method static Builder|SaleType whereCreatedBy($value)
 * @method static Builder|SaleType whereDeletedAt($value)
 * @method static Builder|SaleType whereDeletedBy($value)
 * @method static Builder|SaleType whereId($value)
 * @method static Builder|SaleType whereIsDeleted($value)
 * @method static Builder|SaleType whereName($value)
 * @method static Builder|SaleType whereNotes($value)
 * @method static Builder|SaleType wherePublicId($value)
 * @method static Builder|SaleType whereUpdatedAt($value)
 * @method static Builder|SaleType whereUpdatedBy($value)
 * @method static Builder|SaleType whereUserId($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @method static Builder|SaleType withTrashed()
 * @method static Builder|SaleType withoutTrashed()
 * @mixin Eloquent
 */
	class SaleType extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Notifications\DatabaseNotification;
    use Illuminate\Notifications\DatabaseNotificationCollection;
    use Illuminate\Support\Carbon;

    /**
 * App\Models\Setting
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int $per_page
 * @property string|null $site_name
 * @property int|null $qr_code
 * @property string|null $qr_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $display_asset_name
 * @property int|null $display_checkout_date
 * @property int|null $display_eol
 * @property int $auto_increment_assets
 * @property string|null $auto_increment_prefix
 * @property int $load_remote
 * @property string|null $logo
 * @property string|null $header_color
 * @property string|null $alert_email
 * @property int $alerts_enabled
 * @property string|null $default_eula_text
 * @property string|null $barcode_type
 * @property string|null $slack_endpoint
 * @property string|null $slack_channel
 * @property string|null $slack_botname
 * @property string|null $default_currency
 * @property string|null $custom_css
 * @property int $brand
 * @property string|null $ldap_enabled
 * @property string|null $ldap_server
 * @property string|null $ldap_uname
 * @property string|null $ldap_pword
 * @property string|null $ldap_basedn
 * @property string|null $ldap_filter
 * @property string|null $ldap_username_field
 * @property string|null $ldap_lname_field
 * @property string|null $ldap_fname_field
 * @property string|null $ldap_auth_filter_query
 * @property int|null $ldap_version
 * @property string|null $ldap_active_flag
 * @property string|null $ldap_emp_num
 * @property string|null $ldap_email
 * @property int $full_multiple_companies_support
 * @property int $ldap_server_cert_ignore
 * @property string|null $locale
 * @property int $labels_per_page
 * @property float $labels_width
 * @property float $labels_height
 * @property float $labels_pmargin_left
 * @property float $labels_pmargin_right
 * @property float $labels_pmargin_top
 * @property float $labels_pmargin_bottom
 * @property float $labels_display_bgutter
 * @property float $labels_display_sgutter
 * @property int $labels_fontsize
 * @property float $labels_pagewidth
 * @property float $labels_pageheight
 * @property int $labels_display_name
 * @property int $labels_display_serial
 * @property int $labels_display_tag
 * @property string|null $alt_barcode
 * @property int|null $alt_barcode_enabled
 * @property int|null $alert_interval
 * @property int|null $alert_threshold
 * @property string|null $email_domain
 * @property string|null $email_format
 * @property string|null $username_format
 * @property int $is_ad
 * @property string|null $ad_domain
 * @property string $ldap_port
 * @property int $ldap_tls
 * @property int $zerofill_count
 * @property int $ldap_pw_sync
 * @property int|null $two_factor_enabled
 * @property int $require_accept_signature
 * @property string|null $date_display_format
 * @property string|null $time_display_format
 * @property int $next_auto_tag_base
 * @property string|null $login_note
 * @property int|null $thumbnail_max_h
 * @property int $pwd_secure_uncommon
 * @property string|null $pwd_secure_complexity
 * @property int $pwd_secure_min
 * @property int|null $audit_interval
 * @property int|null $audit_warning_days
 * @property int $show_url_in_emails
 * @property string|null $custom_forgot_pass_url
 * @property int $show_alerts_in_menu
 * @property int $labels_display_company_name
 * @property int $show_archived_in_list
 * @property string|null $dashboard_message
 * @property string|null $support_footer
 * @property string|null $footer_text
 * @property string|null $modellist_displays
 * @property int $login_remote_user_enabled
 * @property int $login_common_disabled
 * @property string $login_remote_user_custom_logout_url
 * @property string|null $skin
 * @property int $show_images_in_email
 * @property string|null $admin_cc_email
 * @property int $labels_display_model
 * @property string|null $privacy_policy_link
 * @property string|null $version_footer
 * @property int $unique_serial
 * @property int $logo_print_assets
 * @property int|null $Column 106
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereAccountId($value)
 * @method static Builder|Setting whereAdDomain($value)
 * @method static Builder|Setting whereAdminCcEmail($value)
 * @method static Builder|Setting whereAlertEmail($value)
 * @method static Builder|Setting whereAlertInterval($value)
 * @method static Builder|Setting whereAlertThreshold($value)
 * @method static Builder|Setting whereAlertsEnabled($value)
 * @method static Builder|Setting whereAltBarcode($value)
 * @method static Builder|Setting whereAltBarcodeEnabled($value)
 * @method static Builder|Setting whereAuditInterval($value)
 * @method static Builder|Setting whereAuditWarningDays($value)
 * @method static Builder|Setting whereAutoIncrementAssets($value)
 * @method static Builder|Setting whereAutoIncrementPrefix($value)
 * @method static Builder|Setting whereBarcodeType($value)
 * @method static Builder|Setting whereBrand($value)
 * @method static Builder|Setting whereColumn106($value)
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereCustomCss($value)
 * @method static Builder|Setting whereCustomForgotPassUrl($value)
 * @method static Builder|Setting whereDashboardMessage($value)
 * @method static Builder|Setting whereDateDisplayFormat($value)
 * @method static Builder|Setting whereDefaultCurrency($value)
 * @method static Builder|Setting whereDefaultEulaText($value)
 * @method static Builder|Setting whereDeletedAt($value)
 * @method static Builder|Setting whereDisplayAssetName($value)
 * @method static Builder|Setting whereDisplayCheckoutDate($value)
 * @method static Builder|Setting whereDisplayEol($value)
 * @method static Builder|Setting whereEmailDomain($value)
 * @method static Builder|Setting whereEmailFormat($value)
 * @method static Builder|Setting whereFooterText($value)
 * @method static Builder|Setting whereFullMultipleCompaniesSupport($value)
 * @method static Builder|Setting whereHeaderColor($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereIsAd($value)
 * @method static Builder|Setting whereLabelsDisplayBgutter($value)
 * @method static Builder|Setting whereLabelsDisplayCompanyName($value)
 * @method static Builder|Setting whereLabelsDisplayModel($value)
 * @method static Builder|Setting whereLabelsDisplayName($value)
 * @method static Builder|Setting whereLabelsDisplaySerial($value)
 * @method static Builder|Setting whereLabelsDisplaySgutter($value)
 * @method static Builder|Setting whereLabelsDisplayTag($value)
 * @method static Builder|Setting whereLabelsFontsize($value)
 * @method static Builder|Setting whereLabelsHeight($value)
 * @method static Builder|Setting whereLabelsPageheight($value)
 * @method static Builder|Setting whereLabelsPagewidth($value)
 * @method static Builder|Setting whereLabelsPerPage($value)
 * @method static Builder|Setting whereLabelsPmarginBottom($value)
 * @method static Builder|Setting whereLabelsPmarginLeft($value)
 * @method static Builder|Setting whereLabelsPmarginRight($value)
 * @method static Builder|Setting whereLabelsPmarginTop($value)
 * @method static Builder|Setting whereLabelsWidth($value)
 * @method static Builder|Setting whereLdapActiveFlag($value)
 * @method static Builder|Setting whereLdapAuthFilterQuery($value)
 * @method static Builder|Setting whereLdapBasedn($value)
 * @method static Builder|Setting whereLdapEmail($value)
 * @method static Builder|Setting whereLdapEmpNum($value)
 * @method static Builder|Setting whereLdapEnabled($value)
 * @method static Builder|Setting whereLdapFilter($value)
 * @method static Builder|Setting whereLdapFnameField($value)
 * @method static Builder|Setting whereLdapLnameField($value)
 * @method static Builder|Setting whereLdapPort($value)
 * @method static Builder|Setting whereLdapPwSync($value)
 * @method static Builder|Setting whereLdapPword($value)
 * @method static Builder|Setting whereLdapServer($value)
 * @method static Builder|Setting whereLdapServerCertIgnore($value)
 * @method static Builder|Setting whereLdapTls($value)
 * @method static Builder|Setting whereLdapUname($value)
 * @method static Builder|Setting whereLdapUsernameField($value)
 * @method static Builder|Setting whereLdapVersion($value)
 * @method static Builder|Setting whereLoadRemote($value)
 * @method static Builder|Setting whereLocale($value)
 * @method static Builder|Setting whereLoginCommonDisabled($value)
 * @method static Builder|Setting whereLoginNote($value)
 * @method static Builder|Setting whereLoginRemoteUserCustomLogoutUrl($value)
 * @method static Builder|Setting whereLoginRemoteUserEnabled($value)
 * @method static Builder|Setting whereLogo($value)
 * @method static Builder|Setting whereLogoPrintAssets($value)
 * @method static Builder|Setting whereModellistDisplays($value)
 * @method static Builder|Setting whereNextAutoTagBase($value)
 * @method static Builder|Setting wherePerPage($value)
 * @method static Builder|Setting wherePrivacyPolicyLink($value)
 * @method static Builder|Setting wherePublicId($value)
 * @method static Builder|Setting wherePwdSecureComplexity($value)
 * @method static Builder|Setting wherePwdSecureMin($value)
 * @method static Builder|Setting wherePwdSecureUncommon($value)
 * @method static Builder|Setting whereQrCode($value)
 * @method static Builder|Setting whereQrText($value)
 * @method static Builder|Setting whereRequireAcceptSignature($value)
 * @method static Builder|Setting whereShowAlertsInMenu($value)
 * @method static Builder|Setting whereShowArchivedInList($value)
 * @method static Builder|Setting whereShowImagesInEmail($value)
 * @method static Builder|Setting whereShowUrlInEmails($value)
 * @method static Builder|Setting whereSiteName($value)
 * @method static Builder|Setting whereSkin($value)
 * @method static Builder|Setting whereSlackBotname($value)
 * @method static Builder|Setting whereSlackChannel($value)
 * @method static Builder|Setting whereSlackEndpoint($value)
 * @method static Builder|Setting whereSupportFooter($value)
 * @method static Builder|Setting whereThumbnailMaxH($value)
 * @method static Builder|Setting whereTimeDisplayFormat($value)
 * @method static Builder|Setting whereTwoFactorEnabled($value)
 * @method static Builder|Setting whereUniqueSerial($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereUserId($value)
 * @method static Builder|Setting whereUsernameFormat($value)
 * @method static Builder|Setting whereVersionFooter($value)
 * @method static Builder|Setting whereZerofillCount($value)
 */
	class Setting extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Size.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Size newModelQuery()
 * @method static Builder|Size newQuery()
 * @method static Builder|Size query()
 * @method static Builder|Size whereCreatedAt($value)
 * @method static Builder|Size whereDeletedAt($value)
 * @method static Builder|Size whereId($value)
 * @method static Builder|Size whereName($value)
 * @method static Builder|Size whereUpdatedAt($value)
 */
	class Size extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Model Class StatusService.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|ItemTransfer[] $itemTransfers
 * @property-read int|null $item_transfers_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static Builder|Status onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Status withTrashed()
 * @method static Builder|Status withoutTrashed()
 */
	class Status extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class Subscription.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $event_id
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property string $target_url
 * @property string|null $format
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription newQuery()
 * @method static Builder|Common\Subscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereTargetUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Common\Subscription whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Common\Subscription withTrashed()
 * @method static Builder|Common\Subscription withoutTrashed()
 */
	class Subscription extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class Task.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $account_id
 * @property int|null $client_id
 * @property int|null $invoice_id
 * @property int|null $public_id
 * @property int|null $project_id
 * @property int|null $task_status_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $description
 * @property int $is_deleted
 * @property int $is_running
 * @property string|null $time_log
 * @property int $task_status_sort_order
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Client|null $client
 * @property-read Invoice|null $invoice
 * @property-read Project|null $project
 * @property-read TaskStatus|null $task_status
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Task dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereIsRunning($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTaskStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTaskStatusSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTimeLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Task withTrashed()
 * @method static Builder|Task withoutTrashed()
 */
	class Task extends Eloquent {}
}

namespace App\Models{

    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class PaymentTerm.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus newQuery()
 * @method static Builder|TaskStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|TaskStatus withTrashed()
 * @method static Builder|TaskStatus withoutTrashed()
 */
	class TaskStatus extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Support\Carbon;

    /**
 * Class Category.
 *
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @method static Builder|TaxCategory newModelQuery()
 * @method static Builder|TaxCategory newQuery()
 * @method static Builder|TaxCategory query()
 * @method static Builder|TaxCategory whereCreatedAt($value)
 * @method static Builder|TaxCategory whereDeletedAt($value)
 * @method static Builder|TaxCategory whereId($value)
 * @method static Builder|TaxCategory whereName($value)
 * @method static Builder|TaxCategory whereUpdatedAt($value)
 */
	class TaxCategory extends Eloquent {}
}

namespace App\Models{

    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class TaxRate.
 *
 * @property int $id
 * @property int $account_id
 * @property int $user_id
 * @property int $public_id
 * @property string|null $name
 * @property float $rate
 * @property int $is_inclusive
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate newQuery()
 * @method static Builder|TaxRate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereIsInclusive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaxRate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|TaxRate withTrashed()
 * @method static Builder|TaxRate withoutTrashed()
 */
	class TaxRate extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Theme.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Theme newModelQuery()
 * @method static Builder|Theme newQuery()
 * @method static Builder|Theme query()
 * @method static Builder|Theme whereCreatedAt($value)
 * @method static Builder|Theme whereDeletedAt($value)
 * @method static Builder|Theme whereId($value)
 * @method static Builder|Theme whereName($value)
 * @method static Builder|Theme whereUpdatedAt($value)
 */
	class Theme extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class Timezone.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $location
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Timezone newModelQuery()
 * @method static Builder|Timezone newQuery()
 * @method static Builder|Timezone query()
 * @method static Builder|Timezone whereCreatedAt($value)
 * @method static Builder|Timezone whereCreatedBy($value)
 * @method static Builder|Timezone whereDeletedAt($value)
 * @method static Builder|Timezone whereDeletedBy($value)
 * @method static Builder|Timezone whereId($value)
 * @method static Builder|Timezone whereLocation($value)
 * @method static Builder|Timezone whereName($value)
 * @method static Builder|Timezone whereUpdatedAt($value)
 * @method static Builder|Timezone whereUpdatedBy($value)
 */
	class Timezone extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Model Class Store.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $notes
 * @property int $is_deleted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static Builder|Token onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Token withTrashed()
 * @method static Builder|Token withoutTrashed()
 */
	class Token extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Model Class Unit.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newQuery()
 * @method static Builder|Unit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Unit withTrashed()
 * @method static Builder|Unit withoutTrashed()
 */
	class Unit extends Eloquent {}
}

namespace App\Models{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;

    /**
 * Class UserAccount.
 *
 * @property int $id
 * @property int|null $user_id1
 * @property int|null $user_id2
 * @property int|null $user_id3
 * @property int|null $user_id4
 * @property int|null $user_id5
 * @method static Builder|UserAccount newModelQuery()
 * @method static Builder|UserAccount newQuery()
 * @method static Builder|UserAccount query()
 * @method static Builder|UserAccount whereId($value)
 * @method static Builder|UserAccount whereUserId1($value)
 * @method static Builder|UserAccount whereUserId2($value)
 * @method static Builder|UserAccount whereUserId3($value)
 * @method static Builder|UserAccount whereUserId4($value)
 * @method static Builder|UserAccount whereUserId5($value)
 */
	class UserAccount extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Model Class Vendor.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $currency_id
 * @property int|null $vendor_type_id
 * @property int|null $hold_reason_id
 * @property int|null $country_id
 * @property int|null $industry_id
 * @property int|null $size_id
 * @property int|null $language_id
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
 * @property int $bill_number_counter
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Collection|VendorContact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read Country|null $country
 * @property-read Collection|BillCredit[] $credits
 * @property-read int|null $credits_count
 * @property-read Collection|BillCredit[] $creditsWithBalance
 * @property-read int|null $credits_with_balance_count
 * @property-read Currency|null $currency
 * @property-read Collection|Expense[] $expenses
 * @property-read int|null $expenses_count
 * @property-read HoldReason|null $holdReason
 * @property-read Industry|null $industry
 * @property-read Collection|Bill[] $invoices
 * @property-read int|null $invoices_count
 * @property-read Language|null $language
 * @property-read Collection|BillPayment[] $payments
 * @property-read int|null $payments_count
 * @property-read Collection|Bill[] $publicQuotes
 * @property-read int|null $public_quotes_count
 * @property-read Collection|Bill[] $quotes
 * @property-read int|null $quotes_count
 * @property-read Country $shipping_country
 * @property-read Size|null $size
 * @property-read User|null $user
 * @property-read VendorType|null $vendorType
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor isInvoiceAllowed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newQuery()
 * @method static Builder|Vendor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereBillNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereBillingAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereBillingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereBillingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereBillingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereBillingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCreditNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCustomMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereHoldReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor wherePaidToDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereQuoteNumberCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereSendReminders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereShippingAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereShippingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereShippingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereShippingPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereShippingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereShowTasksInPortal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereSizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereTaskRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereWorkPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Vendor withTrashed()
 * @method static Builder|Vendor withoutTrashed()
 */
	class Vendor extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class VendorContact.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $vendor_id
 * @property string|null $contact_key
 * @property string|null $bot_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $is_primary
 * @property int $send_bill
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $phone
 * @property string|null $last_login
 * @property string|null $banned_until
 * @property int|null $confirmation_code
 * @property int|null $remember_token
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read User|null $user
 * @property-read Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact newQuery()
 * @method static Builder|VendorContact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereBannedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereBotUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereContactKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereSendBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContact whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|VendorContact withTrashed()
 * @method static Builder|VendorContact withoutTrashed()
 */
	class VendorContact extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Query\Builder;

    /**
 * Class VendorCredit.
 *
 * @property-read Bill $Bill
 * @property-read Account $account
 * @property-read User $user
 * @property-read Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|VendorCredit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorCredit newQuery()
 * @method static Builder|VendorCredit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorCredit query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|VendorCredit withTrashed()
 * @method static Builder|VendorCredit withoutTrashed()
 */
	class VendorCredit extends Eloquent {}
}

namespace App\Models{

    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Class VendorType.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $public_id
 * @property int|null $user_id
 * @property string|null $name
 * @property int $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection|ItemPrice[] $itemPrices
 * @property-read int|null $item_prices_count
 * @property-read Collection|Vendor[] $vendors
 * @property-read int|null $vendors_count
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType newQuery()
 * @method static Builder|VendorType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorType whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|VendorType withTrashed()
 * @method static Builder|VendorType withoutTrashed()
 */
	class VendorType extends Eloquent {}
}

namespace App\Models{

    use App\Models\Common\Account;
    use App\Models\EntityModel;
    use Eloquent;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Query\Builder;
    use Illuminate\Support\Carbon;

    /**
 * Model Class Warehouse.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $location_id
 * @property string|null $name
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Collection|Branch[] $branches
 * @property-read int|null $branches_count
 * @property-read Collection|ItemStore[] $item_stores
 * @property-read int|null $item_stores_count
 * @property-read Location|null $location
 * @property-read User $manager
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse hasQuantity()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newQuery()
 * @method static Builder|Warehouse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Warehouse withTrashed()
 * @method static Builder|Warehouse withoutTrashed()
 */
	class Warehouse extends Eloquent {}
}

namespace App\Notifications{

    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Carbon;

    /**
 * App\Notifications\Notification
 *
 * @property int $id
 * @property string $type
 * @property int $notifiable_id
 * @property string $notifiable_type
 * @property string $data
 * @property string|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Notification newModelQuery()
 * @method static Builder|Notification newQuery()
 * @method static Builder|Notification query()
 * @method static Builder|Notification whereCreatedAt($value)
 * @method static Builder|Notification whereData($value)
 * @method static Builder|Notification whereId($value)
 * @method static Builder|Notification whereNotifiableId($value)
 * @method static Builder|Notification whereNotifiableType($value)
 * @method static Builder|Notification whereReadAt($value)
 * @method static Builder|Notification whereType($value)
 * @method static Builder|Notification whereUpdatedAt($value)
 */
	class Notification extends Eloquent {}
}

