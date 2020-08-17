<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Laracasts\Presenter\PresentableTrait;

/**
 * View Model Class Inventory.
 *
 * @method static Builder|Inventory newModelQuery()
 * @method static Builder|Inventory newQuery()
 * @method static Builder|Inventory query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
class Inventory extends EntityModel
{
    use PresentableTrait;


    protected $presenter = 'App\Ninja\Presenters\InventoryPresenter';

    protected $table = 'view_inventories';

}
