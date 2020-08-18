<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

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
class PaymentStatus extends Eloquent
{
    protected $table = 'payment_statuses';
    public $timestamps = false;
}
