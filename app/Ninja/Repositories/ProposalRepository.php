<?php

namespace App\Ninja\Repositories;

use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\ProposalInvitation;
use App\Models\ProposalTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProposalRepository extends BaseRepository
{
    private $model;

    public function __construct(Proposal $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\Proposal';
    }

    public function all()
    {
        return Proposal::scope()->get();
    }

    public function find($account = false, $filter = null, $userId = false)
    {
        $query = DB::table('proposals')
        ->leftJoin('accounts', 'accounts.id', '=', 'proposals.account_id')
        ->leftJoin('users', 'users.id', '=', 'proposals.user_id')
        ->leftjoin('invoices', 'invoices.id', '=', 'proposals.invoice_id')
        ->leftjoin('clients', 'clients.id', '=', 'invoices.client_id')
        ->leftJoin('contacts', 'contacts.client_id', '=', 'clients.id')
        ->leftJoin('proposal_templates', 'proposal_templates.id', '=', 'proposals.proposal_template_id')
        ->where('proposals.account_id', '=', Auth::user()->account_id)
        ->where('clients.deleted_at', '=', null)
        ->where('contacts.deleted_at', '=', null)
        ->where('contacts.is_primary', '=', true)
        ->select(
            'proposals.public_id',
            'proposals.user_id',
            'proposals.deleted_at',
            'proposals.created_at',
            'proposals.is_deleted',
            'proposals.private_notes',
            'proposals.html as content',
            'proposals.created_at',
            'proposals.updated_at',
            'proposals.deleted_at',
            'proposals.created_by',
            'proposals.updated_by',
            'proposals.deleted_by',
            DB::raw("COALESCE(NULLIF(clients.name,''), NULLIF(CONCAT(contacts.first_name, ' ', contacts.last_name),''), NULLIF(contacts.email,'')) client"),
            'clients.user_id as client_user_id',
            'clients.public_id as client_public_id',
            'invoices.invoice_number as quote',
            'invoices.invoice_number as invoice_number',
            'invoices.public_id as invoice_public_id',
            'invoices.user_id as invoice_user_id',
            'proposal_templates.name as template',
            'proposal_templates.public_id as template_public_id',
            'proposal_templates.user_id as template_user_id'
        );

        $this->applyFilters($query, ENTITY_PROPOSAL);

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('clients.name', 'like', '%' . $filter . '%')
                ->orWhere('contacts.first_name', 'like', '%' . $filter . '%')
                ->orWhere('contacts.last_name', 'like', '%' . $filter . '%')
                ->orWhere('contacts.email', 'like', '%' . $filter . '%')
                ->orWhere('invoices.invoice_number', 'like', '%' . $filter . '%');
            });
        }

        if ($userId) {
            $query->where('proposals.user_id', '=', $userId);
        }

        return $query;
    }

    public function save($input, $proposal = false)
    {
        if (!$proposal) {
            $proposal = Proposal::createNew();
        }

        $proposal->fill($input);

        if (isset($input['invoice_id'])) {
            $proposal->invoice_id = $input['invoice_id'] ? Invoice::getPrivateId($input['invoice_id']) : null;
        }

        if (isset($input['proposal_template_id'])) {
            $proposal->proposal_template_id = $input['proposal_template_id'] ? ProposalTemplate::getPrivateId($input['proposal_template_id']) : null;
        }

        $proposal->save();

        // create invitations
        $contactIds = [];

        foreach ($proposal->invoice->invitations as $invitation) {
            $conactIds[] = $invitation->contact_id;
            $found = false;
            foreach ($proposal->proposal_invitations as $proposalInvitation) {
                if ($invitation->contact_id == $proposalInvitation->contact_id) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $proposalInvitation = ProposalInvitation::createNew();
                $proposalInvitation->proposal_id = $proposal->id;
                $proposalInvitation->contact_id = $invitation->contact_id;
                $proposalInvitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
                $proposalInvitation->save();
            }
        }

        // delete invitations
        foreach ($proposal->proposal_invitations as $proposalInvitation) {
            if (!in_array($proposalInvitation->contact_id, $conactIds)) {
                $proposalInvitation->delete();
            }
        }

        return $proposal;
    }

    public function findInvitationByKey($invitationKey)
    {
        // check for extra params at end of value (from website feature)
        list($invitationKey) = explode('&', $invitationKey);
        $invitationKey = substr($invitationKey, 0, RANDOM_KEY_LENGTH);

        /** @var Invitation $invitation */
        $invitation = ProposalInvitation::where('invitation_key', '=', $invitationKey)->first();
        if (!$invitation) {
            return false;
        }

        $proposal = $invitation->proposal;
        if (!$proposal || $proposal->is_deleted) {
            return false;
        }

        $invoice = $proposal->invoice;
        if (!$invoice || $invoice->is_deleted) {
            return false;
        }

        $client = $invoice->client;
        if (!$client || $client->is_deleted) {
            return false;
        }

        return $invitation;
    }
}
