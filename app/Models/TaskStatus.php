<?php

namespace App\Models;

use App\Models\EntityModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 */
class TaskStatus extends EntityModel
{
    use SoftDeletes;

    protected $table = 'task_statuses';
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function getEntityType()
    {
        return ENTITY_TASK_STATUS;
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task')->orderBy('task_status_sort_order');
    }

}
