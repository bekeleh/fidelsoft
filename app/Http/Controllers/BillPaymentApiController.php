<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillPaymentAPIRequest;
use App\Http\Requests\BillPaymentRequest;
use App\Http\Requests\UpdateBillPaymentRequest;
use App\Models\BillPayment;
use App\Ninja\Mailers\VendorContactMailer;
use App\Ninja\Repositories\BillPaymentRepository;
use App\Services\BillPaymentService;
use Input;
use Response;

class BillPaymentApiController extends BaseAPIController
{
    protected $paymentRepo;
    protected $paymentService;

    protected $entityType = ENTITY_BILL_PAYMENT;

    public function __construct(BillPaymentRepository $paymentRepo, BillPaymentService $paymentService, VendorContactMailer $contactMailer)
    {
        parent::__construct();

        $this->paymentRepo = $paymentRepo;
        $this->paymentService = $paymentService;
        $this->contactMailer = $contactMailer;
    }

    /**
     * @SWG\Get(
     *   path="/payments",
     *   summary="List payments",
     *   operationId="listBillPayments",
     *   tags={"payment"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of payments",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/BillPayment"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $payments = BillPayment::scope()
            ->withTrashed()
            ->with(['bill'])
            ->orderBy('updated_at', 'desc');

        return $this->listResponse($payments);
    }

    /**
     * @SWG\Get(
     *   path="/payments/{payment_id}",
     *   summary="Retrieve a payment",
     *   operationId="getBillPayment",
     *   tags={"payment"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="payment_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single payment",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillPayment"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param BillPaymentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function show(BillPaymentRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/payments",
     *   summary="Create a payment",
     *   operationId="createBillPayment",
     *   tags={"payment"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="payment",
     *     @SWG\Schema(ref="#/definitions/BillPayment")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New payment",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillPayment"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateBillPaymentAPIRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBillPaymentAPIRequest $request)
    {
        // check payment has been marked sent
        $request->bill->markSentIfUnsent();

        $payment = $this->paymentService->save($request->input(), null, $request->bill);

        if (Input::get('email_receipt')) {
            $this->contactMailer->sendBillPaymentConfirmation($payment);
        }

        return $this->itemResponse($payment);
    }

    /**
     * @SWG\Put(
     *   path="/payments/{payment_id}",
     *   summary="Update a payment",
     *   operationId="updateBillPayment",
     *   tags={"payment"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="payment_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="payment",
     *     @SWG\Schema(ref="#/definitions/BillPayment")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated payment",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillPayment"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdateBillPaymentRequest $request
     * @param mixed $publicId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBillPaymentRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $payment = $this->paymentRepo->save($data, $request->entity());

        if (Input::get('email_receipt')) {
            $this->contactMailer->sendBillPaymentConfirmation($payment);
        }

        return $this->itemResponse($payment);
    }

    /**
     * @SWG\Delete(
     *   path="/payments/{payment_id}",
     *   summary="Delete a payment",
     *   operationId="deleteBillPayment",
     *   tags={"payment"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="payment_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted payment",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/BillPayment"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateBillPaymentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(UpdateBillPaymentRequest $request)
    {
        $payment = $request->entity();

        $this->paymentRepo->delete($payment);

        return $this->itemResponse($payment);
    }
}
