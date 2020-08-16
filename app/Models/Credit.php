<?php

namespace App\Models;

use App\Events\CreditWasCreatedEvent;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

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
 * @method static \Illuminate\Database\Eloquent\Builder|Credit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit newQuery()
 * @method static Builder|Credit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Credit query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreditDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereCreditNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|Credit withTrashed()
 * @method static Builder|Credit withoutTrashed()
 * @mixin Eloquent
 */
class Credit extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;


    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $presenter = 'App\Ninja\Presenters\CreditPresenter';


    protected $fillable = [
        'public_notes',
        'private_notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    public function getName()
    {
        return '';
    }

    public function getRoute()
    {
        return "/credits/{$this->public_id}";
    }

    public function getEntityType()
    {
        return ENTITY_CREDIT;
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

Credit::creating(function ($credit) {
});

Credit::created(function ($credit) {
    event(new CreditWasCreatedEvent($credit));
});
