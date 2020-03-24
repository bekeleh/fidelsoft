<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Throttle extends Eloquent
{

    protected $table = 'throttle';

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }
}
