<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillAPIRequest;
use App\Http\Requests\BillRequest;
use App\Http\Requests\UpdateBillAPIRequest;
use App\Jobs\SendBillEmail;
use App\Jobs\SendPaymentEmail;
use App\Libraries\Utils;
use App\Models\Vendor;
use App\Models\Bill;
use App\Models\Product;
use App\Ninja\Repositories\VendorRepository;
use App\Ninja\Repositories\BillRepository;
use App\Ninja\Repositories\PaymentRepository;
use App\Services\BillService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class BillApiController extends BaseAPIController
{
    protected $entityType = ENTITY_INVOICE;
    protected $vendorRepo;
    protected $BillRepo;
    protected $paymentRepo;
    protected $BillService;
    protected $paymentService;

    public function __construct(
        BillService $BillService,
        BillRepository $BillRepo,
        VendorRepository $vendorRepo,
        PaymentRepository $paymentRepo,
        PaymentService $paymentService
    )
    {
        parent::__construct();

        $this->BillRepo = $BillRepo;
        $this->vendorRepo = $vendorRepo;
        $this->paymentRepo = $paymentRepo;
        $this->BillService = $BillService;
        $this->paymentService = $paymentService;
    }

    /**
     * @SWG\Get(
     *   path="/invoices",
     *   summary="List invoices",
     *   operationId="listBills",
     *   tags={"invoice"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of invoices",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Bill"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $Bills = Bill::scope()
            ->withTrashed()
            ->with('BILL_items', 'vendor')
            ->orderBy('updated_at', 'desc');

        // Filter by invoice number
        if ($BillNumber = Input::get('invoice_number')) {
            $Bills->where('invoice_number', $BillNumber);
        }

        // Fllter by status
        if ($statusId = Input::get('status_id')) {
            $Bills->where('invoice_status_id', '>=', $statusId);
        }

        if (request()->has('is_recurring')) {
            $Bills->where('is_recurring', request()->is_recurring);
        }

        if (request()->has('invoice_type_id')) {
            $Bills->where('invoice_type_id', request()->invoice_type_id);
        }

        return $this->listResponse($Bills);
    }

    /**
     * @SWG\Get(
     *   path="/invoices/{invoice_id}",
     *   summary="Retrieve an Bill",
     *   tags={"invoice"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="invoice_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single invoice",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Bill"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param BillRequest $request
     * @return
     */
    public function show(BillRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/invoices",
     *   summary="Create an invoice",
     *   tags={"invoice"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="invoice",
     *     @SWG\Schema(ref="#/definitions/Bill")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New invoice",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Bill"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateBillAPIRequest $request
     * @return
     */
    public function store(CreateBillAPIRequest $request)
    {
        $data = Input::all();
        $error = null;

        if (isset($data['email'])) {
            $email = $data['email'];
            $vendor = Vendor::scope()->whereHas('contacts', function ($query) use ($email) {
                $query->where('email', '=', $email);
            })->first();

            if (!$vendor) {
                $validator = Validator::make(['email' => $email], ['email' => 'email']);
                if ($validator->fails()) {
                    $messages = $validator->messages();

                    return $messages->first();
                }

                $vendorData = ['contact' => ['email' => $email]];
                foreach ([
                             'name',
                             'address1',
                             'address2',
                             'city',
                             'state',
                             'postal_code',
                             'country_id',
                             'private_notes',
                             'currency_code',
                             'country_code',
                         ] as $field) {
                    if (isset($data[$field])) {
                        $vendorData[$field] = $data[$field];
                    }
                }
                foreach ([
                             'first_name',
                             'last_name',
                             'phone',
                         ] as $field) {
                    if (isset($data[$field])) {
                        $vendorData['contact'][$field] = $data[$field];
                    }
                }

                $vendor = $this->vendorRepo->save($vendorData);
            }
        } elseif (isset($data['vendor_id'])) {
            $vendor = Vendor::scope($data['vendor_id'])->first();

            if (!$vendor) {
                return $this->errorResponse('Vendor not found', 404);
            }
        }

        $data = self::prepareData($data, $vendor);
        $data['vendor_id'] = $vendor->id;

        // in these cases the invoice needs to be set as public
        $isAutoBill = isset($data['auto_bill']) && filter_var($data['auto_bill'], FILTER_VALIDATE_BOOLEAN);
        $isEmailBill = isset($data['email_invoice']) && filter_var($data['email_invoice'], FILTER_VALIDATE_BOOLEAN);
        $isPaid = isset($data['paid']) && floatval($data['paid']);

        if ($isAutoBill || $isPaid || $isEmailBill) {
            $data['is_public'] = true;
        }

        $Bill = $this->BillService->save($data);
        $payment = false;

        if ($Bill->isStandard()) {
            if ($isAutoBill) {
                $payment = $this->paymentService->autoBillBill($Bill);
            } elseif ($isPaid) {
                $payment = $this->paymentRepo->save([
                    'invoice_id' => $Bill->id,
                    'vendor_id' => $vendor->id,
                    'amount' => round($data['paid'], 2),
                ]);
            }
        }

        if ($isEmailBill) {
            if ($payment) {
                $this->dispatch(new SendPaymentEmail($payment));
            } else {
                if ($Bill->is_recurring && $recurringBill = $this->BillRepo->createRecurringBill($Bill)) {
                    $Bill = $recurringBill;
                }
                $reminder = isset($data['email_type']) ? $data['email_type'] : false;
                $this->dispatch(new SendBillEmail($Bill, auth()->user()->id, $reminder));
            }
        }

        $Bill = Bill::scope($Bill->public_id)
            ->with('vendor', 'BILL_items', 'invitations')
            ->first();

        if (isset($data['download_invoice']) && boolval($data['download_invoice'])) {
            return $this->fileReponse($Bill->getFileName(), $Bill->getPDFString());
        }

        return $this->itemResponse($Bill);
    }

    private function prepareData($data, $vendor)
    {
        $account = Auth::user()->account;
        $account->loadLocalizationSettings($vendor);

        // set defaults for optional fields
        $fields = [
            'discount' => 0,
            'is_amount_discount' => false,
            'terms' => '',
            'invoice_footer' => '',
            'public_notes' => '',
            'po_number' => '',
            'invoice_design_id' => $account->invoice_design_id,
            'BILL_items' => [],
            'custom_taxes1' => false,
            'custom_taxes2' => false,
            'tax_name1' => '',
            'tax_rate1' => 0,
            'tax_name2' => '',
            'tax_rate2' => 0,
            'partial' => 0,
        ];

        if (!isset($data['invoice_status_id']) || $data['invoice_status_id'] == 0) {
            $data['invoice_status_id'] = INVOICE_STATUS_DRAFT;
        }

        if (!isset($data['invoice_date'])) {
            $fields['invoice_date_sql'] = date_create()->format('Y-m-d');
        }
        if (!isset($data['due_date'])) {
            $fields['due_date_sql'] = false;
        }

        if (isset($data['is_quote']) && filter_var($data['is_quote'], FILTER_VALIDATE_BOOLEAN)) {
            $fields['invoice_design_id'] = $account->quote_design_id;
        }

        foreach ($fields as $key => $val) {
            if (!isset($data[$key])) {
                $data[$key] = $val;
            }
        }

        // initialize the line items
        if (!isset($data['BILL_items']) && (isset($data['name']) || isset($data['cost']) || isset($data['notes']) || isset($data['qty']))) {
            $data['BILL_items'] = [self::prepareItem($data)];
            // make sure the tax isn't applied twice (for the invoice and the line item)
            unset($data['BILL_items'][0]['tax_name1']);
            unset($data['BILL_items'][0]['tax_rate1']);
            unset($data['BILL_items'][0]['tax_name2']);
            unset($data['BILL_items'][0]['tax_rate2']);
        } else {
            foreach ($data['BILL_items'] as $index => $item) {
                // check for multiple products
                if ($productKey = array_get($item, 'name')) {
                    $parts = explode(',', $productKey);
                    if (count($parts) > 1 && Product::findProductByKey($parts[0])) {
                        foreach ($parts as $index => $productKey) {
                            $data['BILL_items'][$index] = self::prepareItem(['name' => $productKey]);
                        }
                        break;
                    }
                }
                $data['BILL_items'][$index] = self::prepareItem($item);
            }
        }

        return $data;
    }

    private function prepareItem($item)
    {
        // if only the product key is set we'll load the cost and notes
        if (!empty($item['name'])) {
            $product = Product::findProductByKey($item['name']);
            if ($product) {
                $fields = [
                    'cost',
                    'notes',
                    'custom_value1',
                    'custom_value2',
                    'tax_name1',
                    'tax_rate1',
                    'tax_name2',
                    'tax_rate2',
                ];
                foreach ($fields as $field) {
                    if (!isset($item[$field])) {
                        $item[$field] = $product->$field;
                    }
                }
            }
        }

        $fields = [
            'cost' => 0,
            'name' => '',
            'notes' => '',
            'qty' => 1,
        ];

        foreach ($fields as $key => $val) {
            if (!isset($item[$key])) {
                $item[$key] = $val;
            }
        }

        // Workaround to support line item taxes w/Zapier
        foreach (['tax_rate1', 'tax_name1', 'tax_rate2', 'tax_name2'] as $field) {
            if (isset($item['item_' . $field])) {
                $item[$field] = $item['item_' . $field];
            }
        }

        return $item;
    }

    public function emailBill(BillRequest $request)
    {
        $Bill = $request->entity();

        if ($Bill->is_recurring && $recurringBill = $this->BillRepo->createRecurringBill($Bill)) {
            $Bill = $recurringBill;
        }

        $reminder = request()->reminder;
        $template = request()->template;

        if (config('queue.default') !== 'sync') {
            $this->dispatch(new SendBillEmail($Bill, auth()->user()->id, $reminder, $template));
        } else {
            $result = app('App\Ninja\Mailers\ContactMailer')->sendBill($Bill, $reminder, $template);
            if ($result !== true) {
                return $this->errorResponse($result, 500);
            }
        }

        $headers = Utils::getApiHeaders();
        $response = json_encode(['message' => RESULT_SUCCESS], JSON_PRETTY_PRINT);

        return Response::make($response, 200, $headers);
    }

    /**
     * @SWG\Put(
     *   path="/invoices/{invoice_id}",
     *   summary="Update an invoice",
     *   tags={"invoice"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="invoice_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="invoice",
     *     @SWG\Schema(ref="#/definitions/Bill")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated invoice",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Bill"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdateBillAPIRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UpdateBillAPIRequest $request, $publicId)
    {
        if ($request->action == ACTION_CONVERT) {
            $quote = $request->entity();
            $Bill = $this->BillRepo->cloneBill($quote, $quote->id);

            return $this->itemResponse($Bill);
        } elseif ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $this->BillService->save($data, $request->entity());

        $Bill = Bill::scope($publicId)
            ->withTrashed()
            ->with('vendor', 'BILL_items', 'invitations')
            ->firstOrFail();

        return $this->itemResponse($Bill);
    }

    /**
     * @SWG\Delete(
     *   path="/invoices/{invoice_id}",
     *   summary="Delete an invoice",
     *   tags={"invoice"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="invoice_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted purchase invoice",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Bill"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateBillAPIRequest $request
     * @return
     */
    public function destroy(UpdateBillAPIRequest $request)
    {
        $Bill = $request->entity();

        $this->BillRepo->delete($Bill);

        return $this->itemResponse($Bill);
    }

    public function download(BillRequest $request)
    {
        $Bill = $request->entity();

        if ($Bill->is_deleted) {
            abort(404);
        }

        $pdfString = $Bill->getPDFString();

        if ($pdfString) {
            return $this->fileReponse($Bill->getFileName(), $pdfString);
        } else {
            abort(404);
        }
    }
}
