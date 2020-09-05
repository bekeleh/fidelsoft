<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillQuoteRequest;
use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\BillInvitation;
use App\Models\Bill;
use App\Models\InvoiceDesign;
use App\Models\Product;
use App\Models\TaxRate;
use App\Ninja\Datatables\BillDatatable;
use App\Ninja\Mailers\VendorMailer as Mailer;
use App\Ninja\Repositories\VendorRepository;
use App\Ninja\Repositories\BillRepository;
use App\Services\BillService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class BillQuoteController extends BaseController
{
    protected $mailer;
    protected $billRepo;
    protected $vendorRepo;
    protected $billService;
    protected $entityType = ENTITY_BILL;

    public function __construct(Mailer $mailer, BillRepository $billRepo, VendorRepository $vendorRepo, BillService $billService)
    {
        // parent::__construct();

        $this->mailer = $mailer;
        $this->billRepo = $billRepo;
        $this->vendorRepo = $vendorRepo;
        $this->billService = $billService;
    }

    public function index()
    {
        $this->authorize('view', ENTITY_BILL_QUOTE);
        $datatable = new BillDatatable();
        $datatable->entityType = ENTITY_BILL_QUOTE;

        $data = [
            'title' => trans('texts.bill_quotes'),
            'entityType' => ENTITY_BILL_QUOTE,
            'datatable' => $datatable,
        ];

        return response()->view('list_wrapper', $data);
    }

    public function getDatatable($vendorPublicId = null)
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->billService
            ->getDatatable($accountId, $vendorPublicId, ENTITY_BILL_QUOTE, $search);
    }

    public function create(BillQuoteRequest $request, $vendorPublicId = 0)
    {
        $this->authorize('create', ENTITY_BILL_QUOTE);
        if (!Utils::hasFeature(FEATURE_QUOTES)) {
            return Redirect::to('/bills/create');
        }

        $account = Auth::user()->account;
        $vendorId = null;
        if ($vendorPublicId) {
            $vendorId = Vendor::getPrivateId($vendorPublicId);
        }

        $bill = $account->createBill(ENTITY_BILL_QUOTE, $vendorId);
        $bill->public_id = 0;

        $data = [
            'entityType' => $bill->getEntityType(),
            'invoice' => $bill,
            'data' => Input::old('data'),
            'method' => 'POST',
            'url' => 'bill_quotes',
            'title' => trans('texts.new_bill_quote'),
        ];

        $data = array_merge($data, self::getViewModel($bill));

        return View::make('bills.edit', $data);
    }

    private static function getViewModel($bill = null)
    {
        $account = Auth::user()->account;

        $userBranch = isset(Auth::user()->branch->id) ? intval(Auth::user()->branch->id) : null;

        return [
            'entityType' => ENTITY_BILL_QUOTE,
            'account' => Auth::user()->account->load('country'),
            'products' => Product::scope()->withActiveOrSelected(isset($bill) ? $bill->product_id : false)->stock(),
            'clients' => Vendor::scope()->with('contacts', 'country')->orderBy('name')->get(),
            'taxRateOptions' => $account->present()->taxRateOptions,
            'taxRates' => TaxRate::scope()->orderBy('name')->get(),
            'sizes' => Cache::get('sizes'),
            'paymentTerms' => Cache::get('paymentTerms'),
            'invoiceDesigns' => InvoiceDesign::getDesigns(),
            'invoiceFonts' => Cache::get('fonts'),
            'invoiceLabels' => Auth::user()->account->getInvoiceLabels(),
            'isRecurring' => false,
            'expenses' => collect(),
        ];
    }

    public function bulk()
    {
        $action = Input::get('bulk_action') ?: Input::get('action');
        $ids = Input::get('bulk_public_id') ?: (Input::get('public_id') ?: Input::get('ids'));

        if ($action == 'convert') {
            $bill = Bill::with('invoice_items')->scope($ids)->firstOrFail();

            $clone = $this->billService->convertQuote($bill);

            Session::flash('message', trans('texts.converted_to_bill'));

            return Redirect::to('bill_quotes/' . $clone->public_id);
        }

        $count = $this->billService->bulk($ids, $action);

        if ($count > 0) {
            if ($action == 'markSent') {
                $key = 'updated_bill_quote';
            } elseif ($action == 'download') {
                $key = 'downloaded_bill_quote';
            } else {
                $key = "{$action}d_bill_quote";
            }
            $message = Utils::pluralize($key, $count);

            Session::flash('message', $message);
        }

        return $this->returnBulk(ENTITY_BILL_QUOTE, $action, $ids);
    }

    public function approve($invitationKey)
    {
        $invitation = BillInvitation::with('bill.invoice_items', 'bill.bill_invitations')->where('invitation_key', $invitationKey)
            ->firstOrFail();
        $bill = $invitation->bill;
        $account = $bill->account;

        if ($account->requiresAuthorization($bill) && !session('authorized:' . $invitation->invitation_key)) {
            return redirect()->to('view/' . $invitation->invitation_key);
        }

        if ($bill->due_date) {
            $carbonDueDate = Carbon::parse($bill->due_date);
            if (!$carbonDueDate->isToday() && !$carbonDueDate->isFuture()) {
                return redirect("view/{$invitationKey}")->withError(trans('texts.quote_has_expired'));
            }
        }

        if ($billInvitationKey = $this->billService->approveQuote($bill, $invitation)) {
            Session::flash('message', trans('texts.bill_quote_is_approved'));
            return Redirect::to("view/{$billInvitationKey}");
        } else {
            return Redirect::to("view/{$invitationKey}");
        }
    }
}
