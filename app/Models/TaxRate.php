<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class TaxRate.
 */
class TaxRate extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\TaxRatePresenter';
    protected $dates = ['deleted_at'];


    protected $fillable = [
        'name',
        'rate',
        'is_inclusive',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_TAX_RATE;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function __toString()
    {
        return sprintf('%s: %s%%', $this->name, $this->rate);
    }
}
