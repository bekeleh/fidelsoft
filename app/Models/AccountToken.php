<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LookupAccountToken;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

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
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken newQuery()
 * @method static Builder|AccountToken onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|AccountToken withTrashed()
 * @method static Builder|AccountToken withoutTrashed()
 * @mixin Eloquent
 */
class AccountToken extends EntityModel
{
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TOKEN;
    }

    /**
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }
}

AccountToken::creating(function ($token) {
    LookupAccountToken::createNew($token->account->account_key, [
        'token' => $token->token,
    ]);
});

AccountToken::deleted(function ($token) {
    if ($token->forceDeleting) {
        LookupAccountToken::deleteWhere([
            'token' => $token->token
        ]);
    }
});
