<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\StoreIntent;

class CreateStoreIntent extends StoreIntent
{
    public function process()
    {
        $url = '/warehouses/create';


        return redirect($url);
    }
}
