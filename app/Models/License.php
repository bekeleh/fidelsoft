<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

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
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static Builder|License onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @method static \Illuminate\Database\Eloquent\Builder|License whereAffiliateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereIsClaimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereLicenseKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedBy($value)
 * @method static Builder|License withTrashed()
 * @method static Builder|License withoutTrashed()
 * @mixin \Eloquent
 */
class License extends Eloquent
{
    protected $table = 'licenses';
    public $timestamps = true;
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
}
