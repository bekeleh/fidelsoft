<?php

namespace App\Models;

use App\Models\EntityModel;
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

    protected $table = 'tax_rates';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    protected $fillable = [
        'name',
        'rate',
        'notes',
        'is_inclusive',
        'is_deleted',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_TAX_RATE;
    }

    public function getRoute()
    {
        return "/tax_rates/{$this->public_id}/edit";
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
