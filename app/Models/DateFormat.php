<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class DateFormat.
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
