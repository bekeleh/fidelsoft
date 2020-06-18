<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Jobs\Client\GenerateStatementData;
use App\Jobs\LoadPostmarkHistory;
use App\Jobs\ReactivatePostmarkEmail;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Expense;
use App\Models\HoldReason;
use App\Models\Invoice;
use App\Models\SaleType;
use App\Models\Task;
use App\Ninja\Datatables\ClientDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Services\ClientService;
use DropdownButton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class ClientController extends BaseController
{
    protected $clientService;
    protected $clientRepo;
    protected $entityType = ENTITY_CLIENT;

    /**
     *
     * ClientController constructor.
     *
     * @param ClientRepository $clientRepo
     * @param ClientService $clientService
     */
    public function __construct(ClientRepository $clientRepo, ClientService $clientService)
    {
        //parent::__construct();

        $this->clientRepo = $clientRepo;
        $this->clientService = $clientService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_CLIENT);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_CLIENT,
            'datatable' => new ClientDatatable(),
            'title' => trans('texts.clients'),
            'statuses' => Client::getStatuses(),
        ]);
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->clientService->getDatatable($accountId, $search);
    }

    public function getDatatableClientType($clientTypePublicId = null)
    {
        return $this->clientService->getDatatableClientType($clientTypePublicId);
    }

    public function getDatatableSaleType($saleTypePublicId = null)
    {
        return $this->clientService->getDatatableSaleType($saleTypePublicId);
    }

    public function getDatatableHoldReason($holdReasonPublicId = null)
    {
        return $this->clientService->getDatatableHoldReason($holdReasonPublicId);
    }

    public function create(ClientRequest $request)
    {
        $this->authorize('create', ENTITY_CLIENT);
        if ($request->client_type_id != 0) {
            $clientType = ClientType::scope($request->client_type_id)->firstOrFail();
        } else {
            $clientType = null;
        }
        if ($request->sale_type_id != 0) {
            $saleType = SaleType::scope($request->sale_type_id)->firstOrFail();
        } else {
            $saleType = null;
        }
        if ($request->hold_reason_id != 0) {
            $holdReason = HoldReason::scope($request->hold_reason_id)->firstOrFail();
        } else {
            $holdReason = null;
        }

        if (Client::scope()->withTrashed()->count() > Auth::user()->getMaxNumClients()) {
            return View::make('error', ['hideHeader' => true, 'error' => "Sorry, you've exceeded the limit of " . Auth::user()->getMaxNumClients() . ' clients']);
        }

        $data = [
            'clientType' => $clientType,
            'saleType' => $saleType,
            'holdReason' => $holdReason,
            'client' => null,
            'method' => 'POST',
            'url' => 'clients',
            'title' => trans('texts.new_client'),
            'clientTypePublicId' => Input::old('clientType') ? Input::old('clientType') : $request->client_type_id,
            'saleTypePublicId' => Input::old('saleType') ? Input::old('saleType') : $request->sale_type_id,
            'holdReasonPublicId' => Input::old('holdReason') ? Input::old('holdReason') : $request->hold_reason_id,
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('clients.edit', $data);
    }

    public function store(CreateClientRequest $request)
    {
        $data = $request->input();

        $client = $this->clientService->save($data);

        return redirect()->to($client->getRoute())->with('success', trans('texts.created_client'));
    }

    public function edit(ClientRequest $request)
    {
        $this->authorize('edit', ENTITY_CLIENT);
        $client = $request->entity();

        $data = [
            'clientType' => null,
            'saleType' => null,
            'holdReason' => null,
            'client' => $client,
            'method' => 'PUT',
            'url' => 'clients/' . $client->public_id,
            'title' => trans('texts.edit_client'),
            'clientTypePublicId' => $client->clientType ? $client->clientType->public_id : null,
            'saleTypePublicId' => $client->saleType ? $client->saleType->public_id : null,
            'holdReasonPublicId' => $client->holdReason ? $client->holdReason->public_id : null,
        ];

        $data = array_merge($data, self::getViewModel());

        if (Auth::user()->account->isNinjaAccount()) {
            if ($account = Account::whereId($client->public_id)->first()) {
                $data['planDetails'] = $account->getPlanDetails(false, false);
            }
        }

        return View::make('clients.edit', $data);
    }

    public function update(UpdateClientRequest $request)
    {
        $data = $request->input();

        $client = $this->clientService->save($data, $request->entity());

        return redirect()->to($client->getRoute())->with('success', trans('texts.updated_client'));
    }

    public function show(ClientRequest $request, $publicId)
    {
        $user = Auth::user();
        $account = $user->account;
        $accountId = $user->account_id;
        $client = $this->clientService->getById($publicId, $accountId);

        if ($client) {
            $this->authorize('view', $client);

            $user->can('view', [ENTITY_CLIENT]);
            $actionLinks = [];
            if ($user->can('create', [ENTITY_INVOICE])) {
                $actionLinks[] = ['label' => trans('texts.new_invoice'), 'url' => URL::to('/invoices/create/' . $publicId)];
            }
            if ($user->can('create', [ENTITY_TASK])) {
                $actionLinks[] = ['label' => trans('texts.new_task'), 'url' => URL::to('/tasks/create/' . $publicId)];
            }
            if (Utils::hasFeature(FEATURE_QUOTES) && $user->can('create', [ENTITY_QUOTE])) {
                $actionLinks[] = ['label' => trans('texts.new_quote'), 'url' => URL::to('/quotes/create/' . $publicId)];
            }
            if ($user->can('create', [ENTITY_RECURRING_INVOICE])) {
                $actionLinks[] = ['label' => trans('texts.new_recurring_invoice'), 'url' => URL::to('/recurring_invoices/create/' . $publicId)];
            }

            if (!empty($actionLinks)) {
                $actionLinks[] = DropdownButton::DIVIDER;
            }

            if ($user->can('create', ENTITY_PAYMENT)) {
                $actionLinks[] = ['label' => trans('texts.enter_payment'), 'url' => URL::to('/payments/create/' . $publicId)];
            }

            if ($user->can('create', ENTITY_CREDIT)) {
                $actionLinks[] = ['label' => trans('texts.enter_credit'), 'url' => URL::to('/credits/create/' . $publicId)];
            }

            if ($user->can('create', ENTITY_EXPENSE)) {
                $actionLinks[] = ['label' => trans('texts.enter_expense'), 'url' => URL::to('/expenses/create/' . $publicId)];
            }

            $token = $client->getGatewayToken();

            $data = [
                'account' => $account,
                'actionLinks' => $actionLinks,
                'showBreadcrumbs' => false,
                'client' => $client,
                'credit' => $client->getTotalCredit(),
                'title' => trans('texts.view_client'),
                'hasRecurringInvoices' => $account->isModuleEnabled(ENTITY_RECURRING_INVOICE) && Invoice::scope()->recurring()->withArchived()->whereClientId($client->id)->count() > 0,
                'hasQuotes' => $account->isModuleEnabled(ENTITY_QUOTE) && Invoice::scope()->quotes()->withArchived()->whereClientId($client->id)->count() > 0,
                'hasTasks' => $account->isModuleEnabled(ENTITY_TASK) && Task::scope()->withArchived()->whereClientId($client->id)->count() > 0,
                'hasExpenses' => $account->isModuleEnabled(ENTITY_EXPENSE) && Expense::scope()->withArchived()->whereClientId($client->id)->count() > 0,
                'gatewayLink' => $token ? $token->gatewayLink() : false,
                'gatewayName' => $token ? $token->gatewayName() : false,
            ];

            return View::make('clients.show', $data);
        }
        return response()->view('errors/403');
    }

    private static function getViewModel($client = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'sizes' => Cache::get('sizes'),
            'customLabel1' => Auth::user()->account->customLabel('client1'),
            'customLabel2' => Auth::user()->account->customLabel('client2'),
            'clientTypes' => ClientType::scope()->withActiveOrSelected($client ? $client->client_type_id : false)->orderBy('name')->get(),
            'saleTypes' => SaleType::scope()->withActiveOrSelected($client ? $client->sale_type_id : false)->orderBy('name')->get(),
            'holdReasons' => HoldReason::scope()->withActiveOrSelected($client ? $client->hold_reason_id : false)->orderBy('name')->get(),
        ];
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'purge' && !auth()->user()->is_admin) {
            return redirect('dashboard')->withError(trans('texts.not_authorized'));
        }

        $count = $this->clientService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_client', $count);

        if ($action == 'purge') {
            return redirect('dashboard')->withMessage($message)->with('message', $message);
        } else {
            return $this->returnBulk(ENTITY_CLIENT, $action, $ids);
        }
    }

    public function statement($clientPublicId)
    {
        $statusId = request()->status_id;
        $startDate = request()->start_date;
        $endDate = request()->end_date;
        $account = Auth::user()->account;

        $client = Client::scope(request()->client_id)->with('contacts')->firstOrFail();

        if (!$startDate) {
            $startDate = Utils::today(false)->modify('-6 month')->format('Y-m-d');
            $endDate = Utils::today(false)->format('Y-m-d');
        }

        if (request()->json) {
            return dispatch(new GenerateStatementData($client, request()->all()));
        }

        $data = [
            'showBreadcrumbs' => false,
            'client' => $client,
            'account' => $account,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('clients.statement', $data);
    }

    public function getEmailHistory()
    {
        $history = dispatch(new LoadPostmarkHistory(request()->email));

        return response()->json($history);
    }

    public function reactivateEmail()
    {
        $result = dispatch(new ReactivatePostmarkEmail(request()->bounce_id));

        return response()->json($result);
    }
}
