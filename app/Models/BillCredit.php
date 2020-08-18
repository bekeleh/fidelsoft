<?php

namespace App\Models;

use App\Events\Purchase\BillCreditWasCreatedEvent;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

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
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit newQuery()
 * @method static Builder|BillCredit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereCreditDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereCreditNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BillCredit whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|BillCredit withTrashed()
 * @method static Builder|BillCredit withoutTrashed()
 * @mixin Eloquent
 */
class BillCredit extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\BillCreditPresenter';

    protected $table = 'bill_credits';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    protected $fillable = [
        'public_notes',
        'private_notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
    }

    public function getName()
    {
        return '';
    }

    public function getRoute()
    {
        return "/BILL_CREDITs/{$this->public_id}/edit";
    }

    public function getEntityType()
    {
        return ENTITY_BILL_CREDIT;
    }

    public function apply($amount)
    {
        if ($amount > $this->balance) {
            $applied = $this->balance;
            $this->balance = 0;
        } else {
            $applied = $amount;
            $this->balance = $this->balance - $amount;
        }

        $this->save();

        return $applied;
    }
}

BillCredit::creating(function ($credit) {
});

BillCredit::created(function ($credit) {
    event(new BillCreditWasCreatedEvent($credit));
});
