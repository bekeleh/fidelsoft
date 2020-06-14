<?php

namespace App\Ninja\Repositories;

use App\Models\VendorContact;
use Illuminate\Support\Facades\DB;

class VendorContactRepository extends BaseRepository
{
    private $model;

    public function __construct(VendorContact $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\VendorContact';
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('vendor_contacts')
            ->where('vendor_contacts.account_id', '=', $accountId)
            ->where('vendor_contacts.is_primary', '=', true)
//            ->where('vendor_contacts.deleted_at', '=', null)
            ->select(
                'vendor_contacts.id',
                'vendor_contacts.public_id',
                'vendor_contacts.user_id',
                'vendor_contacts.first_name',
                'vendor_contacts.last_name',
                'vendor_contacts.email',
                'vendor_contacts.phone',
                'vendor_contacts.is_primary',
                'vendor_contacts.banned_until',
                'vendor_contacts.created_at',
                'vendor_contacts.deleted_at',
                'vendor_contacts.is_deleted',
                'vendor_contacts.created_at',
                'vendor_contacts.updated_at',
                'vendor_contacts.deleted_at',
                'vendor_contacts.created_by',
                'vendor_contacts.updated_by',
                'vendor_contacts.deleted_by'
            );

        $this->applyFilters($query, ENTITY_VENDOR_CONTACT);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('vendor_contacts.name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.last_name', 'like', '%' . $filter . '%')
                    ->orWhere('vendor_contacts.email', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function save($data)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if (!$publicId || intval($publicId) < 0) {
            $contact = VendorContact::createNew();
            //$contact->send_invoice = true;
            $contact->vendor_id = $data['vendor_id'];
            $contact->is_primary = VendorContact::Scope()->where('vendor_id', '=', $contact->vendor_id)->count() == 0;
        } else {
            $contact = VendorContact::scope($publicId)->firstOrFail();
        }

        $contact->fill($data);
        $contact->save();

        return $contact;
    }
}
