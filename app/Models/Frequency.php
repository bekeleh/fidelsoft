<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Str;

/**
 * Class Frequency.
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
