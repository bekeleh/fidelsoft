<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Client;
use App\Ninja\Datatables\ClientContactDatatable;
use App\Ninja\Repositories\ContactRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class ContactService.
 */
class ContactService extends BaseService
{

    protected $contactRepo;

    public function __construct(ContactRepository $contactRepo)
    {
        $this->contactRepo = $contactRepo;
    }

    protected function getRepo()
    {
        return $this->contactRepo;
    }

    public function save($data, $contact = null)
    {
        if (isset($data['client_id']) && $data['client_id']) {
            $data['client_id'] = Client::getPrivateId($data['client_id']);
        }

        return $this->contactRepo->save($data, $contact);
    }

    public function getDatatable($accountId, $search = null)
    {
        $datatable = new ClientContactDatatable(true, true);
        $query = $this->clientContactRepo->find($accountId, $search);
        if (!Utils::hasPermission('view_contact')) {

            $query->where('contacts.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
