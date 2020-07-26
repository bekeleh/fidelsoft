<?php

namespace App\Ninja\Repositories;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class ContactRepository extends BaseRepository
{
    private $model;

    public function __construct(Contact $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return Contact::scope()
            ->withTrashed()
            ->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('contacts')
            ->where('contacts.account_id', $accountId)
            // ->where('contacts.deleted_at', null)
            ->select(
                'contacts.id',
                'contacts.public_id',
                'contacts.first_name',
                'contacts.is_deleted',
                'contacts.notes',
                'contacts.created_at',
                'contacts.updated_at',
                'contacts.deleted_at',
                'contacts.created_by',
                'contacts.updated_by',
                'contacts.deleted_by'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->Where('contacts.email', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.phone', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('contacts.last_name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_CONTACT);

        return $query;
    }

    public function save($data, $contact = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($contact) {
            // do nothing
        } elseif (!$publicId || intval($publicId) < 0) {
            $contact = Contact::createNew();
            $contact->send_invoice = true;
            $contact->client_id = $data['client_id'];
            $contact->is_primary = Contact::scope()->where('client_id', $contact->client_id)->count() == 0;
            $contact->contact_key = strtolower(str_random(RANDOM_KEY_LENGTH));
        } else {
            $contact = Contact::scope($publicId)->firstOrFail();
        }

        $contact->fill($data);
        $contact->save();

        return $contact;
    }
}
