<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillItemRequest;
use App\Http\Requests\BillItemRequest;
use App\Http\Requests\UpdateBillItemRequest;
use App\Libraries\Utils;
use App\Models\BillItem;
use App\Models\Product;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\BillItemRepository;
use App\Ninja\Repositories\PaymentRepository;
use App\Services\BillItemService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class BillItemApiController extends BaseAPIController
{
    protected $entityType = ENTITY_BILL;
    protected $clientRepo;
    protected $invoiceItemRepo;
    protected $paymentRepo;
    protected $invoiceService;
    protected $paymentService;

    public function __construct(
        BillItemService $invoiceService,
        BillItemRepository $invoiceItemRepo,
        ClientRepository $clientRepo,
        PaymentRepository $paymentRepo,
        PaymentService $paymentService)
    {
        parent::__construct();

        $this->invoiceItemRepo = $invoiceItemRepo;
        $this->clientRepo = $clientRepo;
        $this->paymentRepo = $paymentRepo;
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
    }

    /**
     * @SWG\Get(
     *   path="/invoices",
     *   summary="List invoices",
     *   operationId="listBillItems",
     *   tags={"invoice"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of invoices",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/BillItem"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $invoices = BillItem::scope()
            ->withTrashed()
            ->with('invoice_items', 'client')
            ->orderBy('updated_at', 'desc');

        // Filter by invoice number
        if ($invoiceNumber = Input::get('invoice_number')) {
            $invoices->whereInvoiceNumber($invoiceNumber);
        }

        // Fllter by status
        if ($statusId = Input::get('status_id')) {
            $invoices->where('invoice_status_id', '>=', $statusId);
        }

        if (request()->has('is_recurring')) {
            $invoices->where('is_recurring', '=', request()->is_recurring);
        }

        if (request()->has('invoice_type_id')) {
            $invoices->where('invoice_type_id', '=', request()->invoice_type_id);
        }

        return $this->listResponse($invoices);
    }

    /**
     * @SWG\Get(
     *   path="/invoices/{invoice_id}",
     *   summary="Retrieve an BillItem",
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
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillItem"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param BillItemRequest $request
     * @return
     */
    public function show(BillItemRequest $request)
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
     *     @SWG\Schema(ref="#/definitions/BillItem")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New invoice",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillItem"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateBillItemAPIRequest $request
     * @return
     */
    public function store(CreateBillItemAPIRequest $request)
    {

    }

    public function emailBillItem(BillItemRequest $request)
    {
        $invoice = $request->entity();

        if ($invoice->is_recurring && $recurringBillItem = $this->invoiceItemRepo->createRecurringBillItem($invoice)) {
            $invoice = $recurringBillItem;
        }

        $reminder = request()->reminder;
        $template = request()->template;

        if (config('queue.default') !== 'sync') {
            $this->dispatch(new SendBillItemEmail($invoice, auth()->user()->id, $reminder, $template));
        } else {
            $result = app('App\Ninja\Mailers\ClientMailer')->sendBillItem($invoice, $reminder, $template);
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
     *     @SWG\Schema(ref="#/definitions/BillItem")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated invoice",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillItem"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdateBillItemRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UpdateBillItemRequest $request, $publicId)
    {
        if ($request->action == ACTION_CONVERT) {
            $quote = $request->entity();
            $invoice = $this->invoiceItemRepo->cloneBillItem($quote, $quote->id);

            return $this->itemResponse($invoice);
        } elseif ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $this->invoiceService->save($data, $request->entity());

        $invoice = BillItem::scope($publicId)
            ->withTrashed()
            ->with('client', 'invoice_items', 'invitations')
            ->firstOrFail();

        return $this->itemResponse($invoice);
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
     *     description="Deleted invoice",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillItem"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateBillItemRequest $request
     * @return
     */
    public function destroy(UpdateBillItemRequest $request)
    {
        $invoice = $request->entity();

        $this->invoiceItemRepo->delete($invoice);

        return $this->itemResponse($invoice);
    }

    public function download(BillItemRequest $request)
    {
        $invoice = $request->entity();

        if ($invoice->is_deleted) {
            abort(404);
        }

        $pdfString = $invoice->getPDFString();

        if ($pdfString) {
            return $this->fileReponse($invoice->getFileName(), $pdfString);
        } else {
            abort(404);
        }
    }

    private function prepareData($data, $client)
    {
        $account = Auth::user()->account;
        $account->loadLocalizationSettings($client);

        // set defaults for optional fields
        $fields = [
            'discount' => 0,
            'is_amount_discount' => false,
            'terms' => '',
            'invoice_footer' => '',
            'public_notes' => '',
            'po_number' => '',
            'invoice_design_id' => $account->invoice_design_id,
            'invoice_items' => [],
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
        if (!isset($data['invoice_items']) && (isset($data['name']) || isset($data['cost']) || isset($data['notes']) || isset($data['qty']))) {
            $data['invoice_items'] = [self::prepareItem($data)];
            // make sure the tax isn't applied twice (for the invoice and the line item)
            unset($data['invoice_items'][0]['tax_name1']);
            unset($data['invoice_items'][0]['tax_rate1']);
            unset($data['invoice_items'][0]['tax_name2']);
            unset($data['invoice_items'][0]['tax_rate2']);
        } else {
            foreach ($data['invoice_items'] as $index => $item) {
                // check for multiple products
                if ($productKey = array_get($item, 'name')) {
                    $parts = explode(',', $productKey);
                    if (count($parts) > 1 && Product::findProductByKey($parts[0])) {
                        foreach ($parts as $index => $productKey) {
                            $data['invoice_items'][$index] = self::prepareItem(['name' => $productKey]);
                        }
                        break;
                    }
                }
                $data['invoice_items'][$index] = self::prepareItem($item);
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
}
