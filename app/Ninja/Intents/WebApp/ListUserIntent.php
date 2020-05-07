<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListUserIntent extends BaseIntent
{
    public function process()
    {
        $this->loadStates(ENTITY_USER);

        return redirect('/users');
    }
}
