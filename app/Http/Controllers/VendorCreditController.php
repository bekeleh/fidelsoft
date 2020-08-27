<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVendorCreditRequest;
use App\Http\Requests\VendorCreditRequest;
use App\Http\Requests\UpdateVendorCreditRequest;
use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\VendorCredit;
use App\Ninja\Datatables\VendorCreditDatatable;
use App\Ninja\Repositories\VendorCreditRepository;
use App\Services\VendorCreditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class VendorCreditController extends BaseController
{
    protected $creditRepo;
    protected $creditService;
    protected $entityType = ENTITY_VENDOR_CREDIT;

    public function __construct(VendorCreditRepository $creditRepo, VendorCreditService $creditService)
    {
        // parent::__construct();

        $this->creditRepo = $creditRepo;
        $this->creditService = $creditService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_VENDOR_CREDIT);
        return View::make('list_wrapper', [
            'entityType' => ENTITY_VENDOR_CREDIT,
            'datatable' => new VendorCreditDatatable(),
            'title' => trans('texts.vendor_credits'),
        ]);
    }

    public function getDatatable($vendorPublicId = null)
    {
        $search = Input::get('sSearch');

        return $this->creditService->getDatatable($vendorPublicId, $search);
    }

    public function create(VendorCreditRequest $request)
    {
        $this->authorize('create', ENTITY_VENDOR_CREDIT);
        $data = [
            'vendorPublicId' => Input::old('vendor') ? Input::old('vendor') : ($request->vendor_id ?: 0),
            'credit' => null,
            'method' => 'POST',
            'url' => 'vendor_credits',
            'title' => trans('texts.new_credit'),
            'vendors' => Vendor::scope()->with('contacts')->orderBy('name')->get(),
        ];

        return View::make('vendor_credits.edit', $data);
    }

    public function edit($publicId)
    {
        $this->authorize('edit', ENTITY_VENDOR_CREDIT);
        $credit = VendorCredit::withTrashed()->scope($publicId)->firstOrFail();
        $credit->credit_date = Utils::fromSqlDate($credit->credit_date);

        $data = [
            'vendor' => $credit->vendor,
            'vendorPublicId' => $credit->vendor->public_id,
            'credit' => $credit,
            'method' => 'PUT',
            'url' => 'vendor_credits/' . $publicId,
            'title' => 'Edit VendorCredit',
            'vendors' => null,
        ];

        return View::make('vendor_credits.edit', $data);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("vendor_credits/{$publicId}/edit");
    }

    public function update(UpdateVendorCreditRequest $request)
    {
        $credit = $request->entity();

        return $this->save($credit);
    }

    public function store(CreateVendorCreditRequest $request)
    {
        return $this->save();
    }

    private function save($credit = null)
    {
        $credit = $this->creditService->save(Input::all(), $credit);

        $message = $credit->wasRecentlyCreated ? trans('texts.created_credit') : trans('texts.updated_credit');
        Session::flash('message', $message);

        return redirect()->to("vendors/{$credit->vendor->public_id}#vendor_credits");
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');
        $count = $this->creditService->bulk($ids, $action);

        if ($count > 0) {
            $message = Utils::pluralize($action . 'd_credit', $count);
            Session::flash('message', $message);
        }

        return $this->returnBulk(ENTITY_VENDOR_CREDIT, $action, $ids);
    }
}
