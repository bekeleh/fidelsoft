<?php

namespace App\Models;

use App\Models\EntityModel;

class Throttle extends EntityModel
{

    public function user()
    {
        return $this->belongsTo('User');
    }

}
