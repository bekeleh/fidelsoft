<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxRateRequest;
use App\Models\TaxRate;
use App\Ninja\Repositories\TaxRateRepository;

/**
 * Class TaxRateApiController.
 */
class TaxRateApiController extends BaseAPIController
{

    protected $entityType = ENTITY_TAX_RATE;

    protected $taxRateRepo;

    public function __construct(TaxRateRepository $taxRateRepo)
    {
        parent::__construct();

        $this->taxRateRepo = $taxRateRepo;
    }

    /**
     * @SWG\Get(
     *   path="/tax_rates",
     *   summary="List tax_rates",
     *   operationId="listTaxRate",
     *   tags={"tax rate"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of tax_rates",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/TaxRate"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $taxRates = TaxRate::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($taxRates);
    }

    /**
     * @SWG\Get(
     *   path="/tax_rates/{tax_rate_id}",
     *   summary="Retrieve a tax rate",
     *   operationId="getTaxRate",
     *   tags={"tax rate"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="tax_rate_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single tax rate",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/TaxRate"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param TaxRateRequest $request
     * @return
     */
    public function show(TaxRateRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/tax_rates",
     *   summary="Create a tax rate",
     *   operationId="createTaxRate",
     *   tags={"tax rate"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/TaxRate")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New tax rate",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/TaxRate"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param TaxRateRequest $request
     * @return
     */
    public function store(TaxRateRequest $request)
    {
        $taxRate = $this->taxRateRepo->save($request->input());

        return $this->itemResponse($taxRate);
    }

    /**
     * @SWG\Put(
     *   path="/tax_rates/{tax_rate_id}",
     *   summary="Update a tax rate",
     *   operationId="updateTaxRate",
     *   tags={"tax rate"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="tax_rate_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="tax rate",
     *     @SWG\Schema(ref="#/definitions/TaxRate")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated tax rate",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/TaxRate"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param TaxRateRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(TaxRateRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $taxRate = $this->taxRateRepo->save($data, $request->entity());

        return $this->itemResponse($taxRate);
    }

    /**
     * @SWG\Delete(
     *   path="/tax_rates/{tax_rate_id}",
     *   summary="Delete a tax rate",
     *   operationId="deleteTaxRate",
     *   tags={"tax rate"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="tax_rate_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted tax rate",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/TaxRate"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param TaxRateRequest $request
     * @return
     */
    public function destroy(TaxRateRequest $request)
    {
        $taxRate = $request->entity();

        $this->taxRateRepo->delete($taxRate);

        return $this->itemResponse($taxRate);
    }
}
