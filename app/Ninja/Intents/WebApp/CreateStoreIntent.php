<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\StoreIntent;

class CreateStoreIntent extends StoreIntent
{
    public function process()
    {
        $url = '/stores/create';


        return redirect($url);
    }
}
