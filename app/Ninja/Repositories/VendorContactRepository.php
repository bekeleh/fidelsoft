<?php

namespace App\Ninja\Repositories;

use App\Models\Vendor;
use App\Models\VendorContact;

class VendorContactRepository extends BaseRepository
{
    private $model;

    public function __construct(VendorContact $model)
    {
        $this->model = $model;
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }


    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function save($data)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if (!$publicId || intval($publicId) < 0) {
            $contact = VendorContact::createNew();
            //$contact->send_invoice = true;
            $contact->vendor_id = $data['vendor_id'];
            $contact->is_primary = VendorContact::scope()->where('vendor_id', '=', $contact->vendor_id)->count() == 0;
        } else {
            $contact = VendorContact::scope($publicId)->firstOrFail();
        }

        $contact->fill($data);
        $contact->save();

        return $contact;
    }
}
