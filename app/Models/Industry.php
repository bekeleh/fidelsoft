<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Industry.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Industry newModelQuery()
 * @method static Builder|Industry newQuery()
 * @method static Builder|Industry query()
 * @method static Builder|Industry whereCreatedAt($value)
 * @method static Builder|Industry whereCreatedBy($value)
 * @method static Builder|Industry whereDeletedAt($value)
 * @method static Builder|Industry whereDeletedBy($value)
 * @method static Builder|Industry whereId($value)
 * @method static Builder|Industry whereName($value)
 * @method static Builder|Industry whereUpdatedAt($value)
 * @method static Builder|Industry whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Industry extends Eloquent
{

    protected $table = 'industries';
    public $timestamps = false;

    public function getName()
    {
        return $this->name;
    }

    public function getTranslatedName()
    {
        return trans('texts.industry_' . Str::slug($this->name, '_'));
    }

}
