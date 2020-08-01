<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Requests\VendorRequest;
use App\Jobs\LoadPostmarkHistory;
use App\Jobs\ReactivatePostmarkEmail;
use App\Jobs\Vendor\GeneratePurchaseStatementData;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\Vendor;
use App\Ninja\Datatables\VendorDatatable;
use App\Ninja\Repositories\VendorRepository;
use App\Services\VendorService;
use DropdownButton;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class VendorController extends BaseController
{
    protected $vendorService;
    protected $vendorRepo;
    protected $entityType = ENTITY_VENDOR;

    public function __construct(VendorRepository $vendorRepo, VendorService $vendorService)
    {
        //parent::__construct();

        $this->vendorRepo = $vendorRepo;
        $this->vendorService = $vendorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('view', ENTITY_VENDOR);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_VENDOR,
            'datatable' => new VendorDatatable(),
            'title' => trans('texts.vendors'),
            'statuses' => Vendor::getStatuses(),
        ]);
    }

    public function getDatatable()
    {
        $search = Input::get('sSearch');
        $accountId = Auth::user()->account_id;

        return $this->vendorService->getDatatable($accountId, $search);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateVendorRequest $request
     * @return Response
     */
    public function store(CreateVendorRequest $request)
    {
        $vendor = $this->vendorService->save($request->input());

        Session::flash('message', trans('texts.created_vendor'));

        return redirect()->to($vendor->getRoute());
    }

    /**
     * Display the specified resource.
     *
     * @param VendorRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function show(VendorRequest $request)
    {
        $this->authorize('view', ENTITY_VENDOR);

        $vendor = $request->entity();
        $user = Auth::user();
        $account = $user->account;

        $actionLinks = [];
        if ($user->can('create', ENTITY_PURCHASE_INVOICE)) {
            $actionLinks[] = ['label' => trans('texts.new_invoice'), 'url' => URL::to('/purchase_invoices/create/' . $vendor->public_id)];
        }
        if (Utils::hasFeature(FEATURE_QUOTES) && $user->can('create', ENTITY_PURCHASE_QUOTE)) {
            $actionLinks[] = ['label' => trans('texts.new_quote'), 'url' => URL::to('/purchase_quotes/create/' . $vendor->public_id)];
        }
        if ($user->can('create', ENTITY_RECURRING_INVOICE)) {
            $actionLinks[] = ['label' => trans('texts.new_recurring_invoice'), 'url' => URL::to('/recurring_purchase_invoices/create/' . $vendor->public_id)];
        }

        if (!empty($actionLinks)) {
            $actionLinks[] = DropdownButton::DIVIDER;
        }

        if ($user->can('create', ENTITY_PURCHASE_PAYMENT)) {
            $actionLinks[] = ['label' => trans('texts.enter_payment'), 'url' => URL::to('/purchase_payments/create/' . $vendor->public_id)];
        }

        if ($user->can('create', ENTITY_PURCHASE_CREDIT)) {
            $actionLinks[] = ['label' => trans('texts.enter_vendor'), 'url' => URL::to('/purchase_vendors/create/' . $vendor->public_id)];
        }

        if ($user->can('create', ENTITY_PURCHASE_EXPENSE)) {
            $actionLinks[] = ['label' => trans('texts.enter_expense'), 'url' => URL::to('/purchase_expenses/create/' . $vendor->public_id)];
        }

        $token = $vendor->getGatewayToken();

        $data = [
            'account' => $account,
            'actionLinks' => $actionLinks,
            'showBreadcrumbs' => false,
            'vendor' => $vendor,
            'credit' => $vendor->getTotalCredit(),
            'title' => trans('texts.view_vendor'),
            'hasRecurringInvoices' => $account->isModuleEnabled(ENTITY_RECURRING_INVOICE) && PurchaseInvoice::scope()->recurring()->withArchived()->where('vendor_id', $vendor->id)->count() > 0,
            'hasQuotes' => $account->isModuleEnabled(ENTITY_PURCHASE_QUOTE) && PurchaseInvoice::scope()->quotes()->withArchived()->where('vendor_id', $vendor->id)->count() > 0,
            'hasExpenses' => $account->isModuleEnabled(ENTITY_PURCHASE_EXPENSE) && Expense::scope()->withArchived()->where('vendor_id', $vendor->id)->count() > 0,
            'gatewayLink' => $token ? $token->gatewayLink() : false,
            'gatewayName' => $token ? $token->gatewayName() : false,
        ];

        return View::make('vendors.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param VendorRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function create(VendorRequest $request)
    {
        $this->authorize('create', ENTITY_VENDOR);

        if (Vendor::scope()->withTrashed()->count() > Auth::user()->getMaxNumVendors()) {
            return View::make('error', ['hideHeader' => true, 'error' => "Sorry, you've exceeded the limit of " . Auth::user()->getMaxNumVendors() . ' vendors']);
        }

        $data = [
            'vendor' => null,
            'method' => 'POST',
            'url' => 'vendors',
            'title' => trans('texts.new_vendor'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('vendors.edit', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param VendorRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(VendorRequest $request)
    {
        $this->authorize('edit', ENTITY_VENDOR);
        $vendor = $request->entity();

        $data = [
            'vendor' => $vendor,
            'method' => 'PUT',
            'url' => 'vendors/' . $vendor->public_id,
            'title' => trans('texts.edit_vendor'),
        ];

        $data = array_merge($data, self::getViewModel());

        if (Auth::user()->account->isNinjaAccount()) {
            if ($account = Account::whereId($vendor->public_id)->first()) {
                $data['planDetails'] = $account->getPlanDetails(false, false);
            }
        }

        return View::make('vendors.edit', $data);
    }

    private static function getViewModel()
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
            'sizes' => Cache::get('sizes'),
            'customLabel1' => Auth::user()->account->customLabel('vendor1'),
            'customLabel2' => Auth::user()->account->customLabel('vendor2'),
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateVendorRequest $request
     * @return Response
     */
    public function update(UpdateVendorRequest $request)
    {
        $vendor = $this->vendorService->save($request->input(), $request->entity());

        Session::flash('message', trans('texts.updated_vendor'));

        return redirect()->to($vendor->getRoute());
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'purge' && !auth()->user()->is_admin) {
            return redirect('dashboard')->withError(trans('texts.not_authorized'));
        }

        $count = $this->vendorService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_vendor', $count);
        Session::flash('message', $message);

        if ($action == 'purge') {
            return redirect('dashboard')->withMessage($message);
        } else {
            return $this->returnBulk(ENTITY_VENDOR, $action, $ids);
        }
    }

    public function statement($vendorPublicId)
    {
        $statusId = request()->status_id;
        $startDate = request()->start_date;
        $endDate = request()->end_date;
        $account = Auth::user()->account;
        $vendor = Vendor::scope(request()->vendor_id)->with('contacts')->firstOrFail();

        if (!$startDate) {
            $startDate = Utils::today(false)->modify('-6 month')->format('Y-m-d');
            $endDate = Utils::today(false)->format('Y-m-d');
        }

        if (request()->json) {
            return dispatch_now(new GeneratePurchaseStatementData($vendor, request()->all()));
        }

        $data = [
            'showBreadcrumbs' => false,
            'vendor' => $vendor,
            'account' => $account,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('vendors.statement', $data);
    }

    public function getEmailHistory()
    {
        $history = dispatch_now(new LoadPostmarkHistory(request()->email));

        return response()->json($history);
    }

    public function reactivateEmail()
    {
        $result = dispatch_now(new ReactivatePostmarkEmail(request()->bounce_id));

        return response()->json($result);
    }
}
