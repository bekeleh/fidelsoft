<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVendorCreditRequest;
use App\Http\Requests\VendorCreditRequest;
use App\Http\Requests\UpdateVendorCreditRequest;
use App\Models\VendorCredit;
use App\Ninja\Repositories\VendorCreditRepository;

class VendorCreditApiController extends BaseAPIController
{
    protected $creditRepo;

    protected $entityType = ENTITY_VENDOR_CREDIT;

    public function __construct(VendorCreditRepository $creditRepo)
    {
        parent::__construct();

        $this->creditRepo = $creditRepo;
    }

    /**
     * @SWG\Get(
     *   path="/credits",
     *   summary="List credits",
     *   operationId="listVendorCredits",
     *   tags={"credit"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of credits",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/VendorCredit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $credits = VendorCredit::scope()
            ->withTrashed()
            ->with(['vendor'])
            ->orderBy('updated_at', 'desc');

        return $this->listResponse($credits);
    }

    /**
     * @SWG\Get(
     *   path="/credits/{credit_id}",
     *   summary="Retrieve a credit",
     *   operationId="getVendorCredit",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="credit_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/VendorCredit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param VendorCreditRequest $request
     * @return
     */
    public function show(VendorCreditRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/credits",
     *   summary="Create a credit",
     *   operationId="createVendorCredit",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="credit",
     *     @SWG\Schema(ref="#/definitions/VendorCredit")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/VendorCredit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateVendorCreditRequest $request
     * @return
     */
    public function store(CreateVendorCreditRequest $request)
    {
        $credit = $this->creditRepo->save($request->input());

        return $this->itemResponse($credit);
    }

    /**
     * @SWG\Put(
     *   path="/credits/{credit_id}",
     *   summary="Update a credit",
     *   operationId="updateVendorCredit",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="credit_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="credit",
     *     @SWG\Schema(ref="#/definitions/VendorCredit")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/VendorCredit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdateVendorCreditRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UpdateVendorCreditRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $credit = $this->creditRepo->save($data, $request->entity());

        return $this->itemResponse($credit);
    }

    /**
     * @SWG\Delete(
     *   path="/credits/{credit_id}",
     *   summary="Delete a credit",
     *   operationId="deleteVendorCredit",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="credit_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/VendorCredit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateVendorCreditRequest $request
     * @return
     */
    public function destroy(UpdateVendorCreditRequest $request)
    {
        $credit = $request->entity();

        $this->creditRepo->delete($credit);

        return $this->itemResponse($credit);
    }
}
