<?php

namespace App\Events;

use App\Models\SaleType;
use Illuminate\Queue\SerializesModels;

/**
 * Class SaleTypeWasDeleted.
 */
class SaleTypeWasCreated extends Event
{
    use SerializesModels;

    /**
     * @var SaleType
     */
    public $saleType;

    /**
     * Create a new event instance.
     *
     * @param SaleType $saleType
     */
    public function __construct(SaleType $saleType)
    {
        $this->saleType = $saleType;
    }
}
