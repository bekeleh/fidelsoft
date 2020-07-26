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
        $query = DB::table('contacts')
            ->leftJoin('accounts', 'accounts.id', '=', 'contacts.account_id')
            ->leftJoin('users', 'users.id', '=', 'contacts.user_id')
            ->where('contacts.account_id', $accountId)
            ->where('contacts.is_primary', true)
//            ->where('contacts.deleted_at', null)
            ->select(
                'contacts.id',
                'contacts.public_id',
                'contacts.user_id',
                'contacts.first_name',
                'contacts.last_name',
                'contacts.email',
                'contacts.phone',
                'contacts.is_primary',
                'contacts.banned_until',
                'contacts.created_at',
                'contacts.deleted_at',
                'contacts.is_deleted',
                'contacts.created_at',
                'contacts.updated_at',
                'contacts.deleted_at',
                'contacts.created_by',
                'contacts.updated_by',
                'contacts.deleted_by'
            );

        $this->applyFilters($query, ENTITY_VENDOR_CONTACT);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.phone', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function save($data)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if (!$publicId || intval($publicId) < 0) {
            $contact = VendorContact::createNew();
            $contact->send_invoice = true;
            $contact->vendor_id = $data['vendor_id'];
            $contact->is_primary = VendorContact::scope()->where('vendor_id', $contact->vendor_id)->count() == 0;
        } else {
            $contact = VendorContact::scope($publicId)->firstOrFail();
        }

        $contact->fill($data);
        $contact->save();

        return $contact;
    }
}
