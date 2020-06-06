<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 *
 * InventoryComposer.php.
 *
 *
 */
class InventoryComposer
{

    public function compose(View $view)
    {
        $view->with('units', Cache::get('units')->each(function ($units) {
            $units->name = trans('texts.unit_' . $units->name);
        })->sortBy(function ($units) {
            return $units->name;
        }));

    }
}
