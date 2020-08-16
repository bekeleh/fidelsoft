<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Frequency.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $date_interval
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Frequency newModelQuery()
 * @method static Builder|Frequency newQuery()
 * @method static Builder|Frequency query()
 * @method static Builder|Frequency whereCreatedAt($value)
 * @method static Builder|Frequency whereDateInterval($value)
 * @method static Builder|Frequency whereDeletedAt($value)
 * @method static Builder|Frequency whereId($value)
 * @method static Builder|Frequency whereName($value)
 * @method static Builder|Frequency whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Frequency extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public static function selectOptions()
    {
        $data = [];

        foreach (Cache::get('frequencies') as $frequency) {
            $name = Str::snake(str_replace(' ', '_', $frequency->name));
            $data[$frequency->id] = trans('texts.freq_' . $name);
        }

        return $data;
    }
}
