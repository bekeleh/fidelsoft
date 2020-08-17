<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

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
 * @mixin \Eloquent
 */
class BillStatus extends Eloquent
{
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
