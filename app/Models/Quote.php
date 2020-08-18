<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExpenseCategory.
 *
 * @method static Builder|Quote newModelQuery()
 * @method static Builder|Quote newQuery()
 * @method static Builder|Quote query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
class Quote extends EntityModel
{

    protected $table = 'invoices';
}