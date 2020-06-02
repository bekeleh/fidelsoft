<?php

namespace App\Models;

use Laracasts\Presenter\PresentableTrait;

/**
 * View Model Class Inventory.
 */
class Inventory extends EntityModel
{
    use PresentableTrait;


    protected $presenter = 'App\Ninja\Presenters\InventoryPresenter';

    protected $table = 'view_inventories';

}
