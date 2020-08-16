<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DatetimeFormat.
 *
 * @property int $id
 * @property string|null $format
 * @property string|null $format_moment
 * @property string|null $format_dart
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|DatetimeFormat newModelQuery()
 * @method static Builder|DatetimeFormat newQuery()
 * @method static Builder|DatetimeFormat query()
 * @method static Builder|DatetimeFormat whereCreatedAt($value)
 * @method static Builder|DatetimeFormat whereDeletedAt($value)
 * @method static Builder|DatetimeFormat whereFormat($value)
 * @method static Builder|DatetimeFormat whereFormatDart($value)
 * @method static Builder|DatetimeFormat whereFormatMoment($value)
 * @method static Builder|DatetimeFormat whereId($value)
 * @method static Builder|DatetimeFormat whereUpdatedAt($value)
 * @mixin Eloquent
 */
class DatetimeFormat extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return bool|string
     */
    public function __toString()
    {
        $date = mktime(0, 0, 0, 12, 31, date('Y'));

        return date($this->format, $date);
    }
}
