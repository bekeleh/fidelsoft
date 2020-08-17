<?php

namespace App\Models;

use App\Models\EntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Carbon;

/**
 * Class Affiliate.
 *
 * @property int $id
 * @property string|null $affiliate_key
 * @property string|null $name
 * @property string|null $payment_title
 * @property string|null $payment_subtitle
 * @property float|null $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Affiliate newModelQuery()
 * @method static Builder|Affiliate newQuery()
 * @method static Builder|Affiliate query()
 * @method static Builder|Affiliate whereAffiliateKey($value)
 * @method static Builder|Affiliate whereCreatedAt($value)
 * @method static Builder|Affiliate whereCreatedBy($value)
 * @method static Builder|Affiliate whereDeletedAt($value)
 * @method static Builder|Affiliate whereDeletedBy($value)
 * @method static Builder|Affiliate whereId($value)
 * @method static Builder|Affiliate whereName($value)
 * @method static Builder|Affiliate wherePaymentSubtitle($value)
 * @method static Builder|Affiliate wherePaymentTitle($value)
 * @method static Builder|Affiliate wherePrice($value)
 * @method static Builder|Affiliate whereUpdatedAt($value)
 * @method static Builder|Affiliate whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Affiliate extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var bool
     */
    protected $softDelete = true;
}
