<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

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
 * @mixin \Eloquent
 */
class InvoiceStatus extends Eloquent
{
    protected $table = 'invoice_statuses';
    public $timestamps = false;

    public static function getIdFromAlias($status)
    {
        switch ($status) {
            case 'draft':
                return INVOICE_STATUS_DRAFT;
            case 'sent':
                return INVOICE_STATUS_SENT;
            case 'viewed':
                return INVOICE_STATUS_VIEWED;
            case 'approved':
                return INVOICE_STATUS_APPROVED;
            case 'partial':
                return INVOICE_STATUS_PARTIAL;
            case 'overdue':
                return INVOICE_STATUS_OVERDUE;
            case 'unpaid':
                return INVOICE_STATUS_UNPAID;
            default:
                return false;
        }
    }


    public function getTranslatedName()
    {
        return trans('texts.status_' . Str::slug($this->name, '_'));
    }
}
