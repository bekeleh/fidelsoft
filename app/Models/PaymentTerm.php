<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class PaymentTerm.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int $num_days
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm newQuery()
 * @method static Builder|PaymentTerm onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereNumDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerm whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|PaymentTerm withTrashed()
 * @method static Builder|PaymentTerm withoutTrashed()
 * @mixin Eloquent
 */
class PaymentTerm extends EntityModel
{
    use SoftDeletes;

    protected $table = 'payment_terms';
    public $timestamps = true;

    protected $dates = ['deleted_at'];


    public function getEntityType()
    {
        return ENTITY_PAYMENT_TERM;
    }

    public function getNumDays()
    {
        return $this->num_days == -1 ? 0 : $this->num_days;
    }

    public static function getSelectOptions()
    {
        // default payment terms
        $terms = PaymentTerm::where('account_id',null)->get();

//      if any client payment terms
        foreach (PaymentTerm::scope()->get() as $term) {
            $terms->push($term);
        }

        foreach ($terms as $term) {
            $term->name = trans('texts.payment_terms_net') . ' ' . $term->getNumDays();
        }

        return $terms->sortBy('num_days');
    }

}
