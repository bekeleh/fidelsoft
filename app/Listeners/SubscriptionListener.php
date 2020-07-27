<?php

namespace App\Listeners;

use App\Events\InvoiceItemsWereCreated;
use App\Events\InvoiceItemsWereUpdated;
use App\Events\InvoiceWasDeleted;
use App\Ninja\Transformers\InvoiceTransformer;

/**
 * Class SubscriptionListener.
 */
class SubscriptionListener extends EntityListener
{


}
