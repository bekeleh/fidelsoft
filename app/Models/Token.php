<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Model Class Store.
 */
class Token extends EntityModel
{
    protected $presenter = 'App\Ninja\Presenters\TokenPresenter';
    use PresentableTrait;
    use SoftDeletes;

    protected $appends = [];
    protected $table = 'tokens';
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
        return ENTITY_TOKEN;
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

}
