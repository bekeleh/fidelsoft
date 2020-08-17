<?php

namespace App\Models;

use App\Models\EntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class DateFormat.
 *
 * @property int $id
 * @property string|null $format
 * @property string|null $picker_format
 * @property string|null $format_moment
 * @property string|null $format_dart
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|DateFormat newModelQuery()
 * @method static Builder|DateFormat newQuery()
 * @method static Builder|DateFormat query()
 * @method static Builder|DateFormat whereCreatedAt($value)
 * @method static Builder|DateFormat whereDeletedAt($value)
 * @method static Builder|DateFormat whereFormat($value)
 * @method static Builder|DateFormat whereFormatDart($value)
 * @method static Builder|DateFormat whereFormatMoment($value)
 * @method static Builder|DateFormat whereId($value)
 * @method static Builder|DateFormat wherePickerFormat($value)
 * @method static Builder|DateFormat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DateFormat extends Eloquent
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
