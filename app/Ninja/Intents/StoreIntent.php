<?php

namespace App\Ninja\Intents;

class StoreIntent extends BaseIntent
{
    public function __construct($state, $data)
    {
        $this->storeRepo = app('App\Ninja\Repositories\StoreRepository');

        parent::__construct($state, $data);
    }
}
