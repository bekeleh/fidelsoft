<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\ProductIntent;

class CreateProductIntent extends ProductIntent
{
    public function process()
    {
        $url = '/products/create';


        return redirect($url);
    }
}
