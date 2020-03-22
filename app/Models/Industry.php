<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Industry.
 */
class Industry extends Eloquent
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
