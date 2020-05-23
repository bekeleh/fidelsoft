<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ScheduleCategory.
 */
class ScheduleCategory extends EntityModel
{
    use SoftDeletes;
    use PresentableTrait;

    protected $presenter = 'App\Ninja\Presenters\ScheduleCategoryPresenter';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'text_color',
        'bg_color',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_SCHEDULE_CATEGORY;
    }

    public function getRoute()
    {
        return "/schedule_categories/{$this->public_id}/edit";
    }

}
