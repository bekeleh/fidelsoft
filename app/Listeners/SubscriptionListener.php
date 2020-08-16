<?php

namespace App\Listeners;

use App\Events\InvoiceItemsWereCreatedEvent;
use App\Events\InvoiceItemsWereUpdatedEvent;
use App\Events\InvoiceWasDeletedEvent;
use App\Ninja\Transformers\InvoiceTransformer;

/**
 * Class SubscriptionListener.
 */
class SubscriptionListener extends EntityListener
{


}
