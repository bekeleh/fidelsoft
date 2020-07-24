<?php

namespace App\Models\Traits;

/**
 * Class OwnedByClientTrait.
 */
trait OwnedByClientTrait
{
    /**
     * @return bool
     */
    public function isClientTrashed()
    {
        if (!$this->client) {
            return false;
        }

        return $this->client->trashed();
    }
}
