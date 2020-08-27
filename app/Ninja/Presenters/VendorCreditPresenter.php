<?php

namespace App\Ninja\Presenters;

use App\Libraries\Utils;
use DateTime;

/**
 * Class CreditPresenter.
 */
class VendorCreditPresenter extends EntityPresenter
{
    /**
     * @return string
     */
    public function vendor()
    {
        return $this->entity->vendor ? $this->entity->vendor->getDisplayName() : '';
    }

    /**
     * @return DateTime|string
     */
    public function credit_date()
    {
        return Utils::fromSqlDate($this->entity->credit_date);
    }
}
