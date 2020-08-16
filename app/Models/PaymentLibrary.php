<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

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
class PaymentLibrary extends Eloquent
{
    /**
     * @var string
     */
    protected $table = 'payment_libraries';
    /**
     * @var bool
     */
    public $timestamps = true;

    public function gateways()
    {
        return $this->hasMany('App\Models\Gateway', 'payment_library_id');
    }
}
