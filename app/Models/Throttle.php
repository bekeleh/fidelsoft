<?php

namespace App\Models;

class Throttle extends EntityModel
{

    protected $table = 'throttle';

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }
}
