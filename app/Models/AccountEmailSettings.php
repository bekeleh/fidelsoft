<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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
class AccountEmailSettings extends Eloquent
{
    /**
     * @var array
     */
    protected $fillable = [
        'bcc_email',
        'reply_to_email',
        'email_subject_invoice',
        'email_subject_quote',
        'email_subject_payment',
        'email_template_invoice',
        'email_template_quote',
        'email_template_payment',
        'email_subject_reminder1',
        'email_subject_reminder2',
        'email_subject_reminder3',
        'email_template_reminder1',
        'email_template_reminder2',
        'email_template_reminder3',
        'late_fee1_amount',
        'late_fee1_percent',
        'late_fee2_amount',
        'late_fee2_percent',
        'late_fee3_amount',
        'late_fee3_percent',
    ];

    public static $templates = [
        TEMPLATE_INVOICE,
        TEMPLATE_QUOTE,
        TEMPLATE_PROPOSAL,
        //TEMPLATE_PARTIAL,
        TEMPLATE_PAYMENT,
        TEMPLATE_REMINDER1,
        TEMPLATE_REMINDER2,
        TEMPLATE_REMINDER3,
        TEMPLATE_REMINDER4,
    ];

}
