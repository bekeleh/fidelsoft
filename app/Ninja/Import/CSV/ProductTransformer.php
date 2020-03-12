<?php

namespace App\Ninja\Import\CSV;

use App\Ninja\Import\BaseTransformer;
use League\Fractal\Resource\Item;

/**
 * Class ProductTransformer.
 */
class ProductTransformer extends BaseTransformer
{
    /**
     * @param $data
     *
     * @return bool|Item
     */
    public function transform($data)
    {
        if (empty($data->name)) {
            return false;
        }

        return new Item($data, function ($data) {
            return [
                'public_id' => $this->getProduct($data, 'name', 'public_id'),
                'name' => $this->getString($data, 'name'),
                'notes' => $this->getString($data, 'notes'),
                'cost' => $this->getFloat($data, 'cost'),
                'custom_value1' => $this->getString($data, 'custom_value1'),
                'custom_value2' => $this->getString($data, 'custom_value2'),
            ];
        });
    }
}
