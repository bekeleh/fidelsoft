<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class ApprovalStatusService.
 */
class ApprovalStatus extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\ApprovalStatusPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $appends = [];
    protected $table = 'approval_statuses';
    protected $dates = ['created_at', 'deleted_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [];
    protected $casts = [];


    public function getEntityType()
    {
        return ENTITY_APPROVAL_STATUS;
    }

    public function getRoute()
    {
        return "/item_categories/{$this->public_id}/edit";
    }

    public static function findApprovalStatusByKey($key)
    {
        return self::scope()->where('name', '=', $key)->first();
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTrashed();
    }

    public function itemTransfers()
    {
        return $this->hasMany('App\Models\ItemTransfer', 'approval_status_id')->withTrashed();
    }
}
