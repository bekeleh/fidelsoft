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


    public function getEntityreason()
    {
        return ENTITY_HOLD_REASON;
    }


    public static function findHoldReasonByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client')->withTrashed();
    }

    public function getRoute()
    {
        return "/hold_reasons/{$this->public_id}/edit";
    }

    public static function allowInvoice()
    {
        $allowInvoice = [
            '0' => DENIED, 
            '1' => ALLOWED
        ];

        return $allowInvoice;
    }

    public static function selectOptions()
    {
        $reasons = HoldReason::where('account_id', null)->get();

        foreach (HoldReason::scope()->get() as $reason) {
            $reasons->push($reason);
        }

        foreach($reasons as $reason){
            $name = Str::snake(str_replace(' ', '_', $reason->name));
            $reasons->name = trans('texts.hold_reason_' . $name);
        }

        return $reasons->sortBy('name');
    }
}
