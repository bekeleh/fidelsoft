<?php

namespace App\Models;

use App\Models\EntityModel;

class Throttle extends EntityModel
{
    protected $table = 'throttles';

    public function user()
    {
        return $this->belongsTo('User');
    }

}
