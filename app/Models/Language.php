<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Language.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $locale
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereCreatedAt($value)
 * @method static Builder|Language whereCreatedBy($value)
 * @method static Builder|Language whereDeletedAt($value)
 * @method static Builder|Language whereDeletedBy($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereLocale($value)
 * @method static Builder|Language whereName($value)
 * @method static Builder|Language whereUpdatedAt($value)
 * @method static Builder|Language whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Language extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
