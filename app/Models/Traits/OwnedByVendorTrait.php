<?php

namespace App\Models\Traits;

/**
 * Class OwnedByClientTrait.
 */
trait OwnedByVendorTrait
{
    /**
     * @return bool
     */
    public function isVendorTrashed()
    {
        if (!$this->vendor) {
            return false;
        }

        return $this->vendor->trashed();
    }
}
