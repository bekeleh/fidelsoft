<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Model Store.
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


    public function getEntityType()
    {
        return ENTITY_HOLD_REASON;
    }


    public static function findHoldReasonByKey($key)
    {
        return self::Scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client', 'hold_reason_id')->withTrashed();
    }

    public function getRoute()
    {
        return "/hold_reasons/{$this->public_id}/edit";
    }

    public static function getSelectOptions()
    {
        $allowInvoice = ['0' => 'deny', '1' => 'allow'];

        return $allowInvoice;
    }
}
