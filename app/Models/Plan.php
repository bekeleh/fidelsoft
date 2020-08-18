<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class Category.
 *
 * @property int $id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Plan newModelQuery()
 * @method static Builder|Plan newQuery()
 * @method static Builder|Plan query()
 * @method static Builder|Plan whereCreatedAt($value)
 * @method static Builder|Plan whereDeletedAt($value)
 * @method static Builder|Plan whereId($value)
 * @method static Builder|Plan whereName($value)
 * @method static Builder|Plan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Plan extends Eloquent
{

    protected $table = 'plans';
    public $timestamps = true;

    protected $softDelete = true;

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.plan_' . Str::slug($this->name, '_'));
    }
}
