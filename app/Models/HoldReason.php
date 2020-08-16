<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

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
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason newQuery()
 * @method static Builder|HoldReason onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereAllowInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HoldReason whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|HoldReason withTrashed()
 * @method static Builder|HoldReason withoutTrashed()
 * @mixin Eloquent
 */
class HoldReason extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\HoldReasonPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $appends = [];
    protected $table = 'hold_reasons';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'allow_invoice',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [];
    protected $casts = [];


    public function getEntityreason()
    {
        return ENTITY_HOLD_REASON;
    }


    public static function findHoldReasonByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client')->withTrashed();
    }

    public function getRoute()
    {
        return "/hold_reasons/{$this->public_id}/edit";
    }

    public static function allowInvoice()
    {
        $allowInvoice = [
            '0' => DENIED, 
            '1' => ALLOWED
        ];

        return $allowInvoice;
    }

    public static function selectOptions()
    {
        $reasons = HoldReason::where('account_id', null)->get();

        foreach (HoldReason::scope()->get() as $reason) {
            $reasons->push($reason);
        }

        foreach($reasons as $reason){
            $name = Str::snake(str_replace(' ', '_', $reason->name));
            $reasons->name = trans('texts.hold_reason_' . $name);
        }

        return $reasons->sortBy('name');
    }
}
