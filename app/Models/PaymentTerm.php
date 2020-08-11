<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 */
class PaymentTerm extends EntityModel
{
    use SoftDeletes;


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
