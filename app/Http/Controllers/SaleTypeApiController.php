<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleTypeRequest;
use App\Models\SaleType;
use App\Ninja\Repositories\SaleTypeRepository;

/**
 * Class SaleTypeApiController.
 */
class SaleTypeApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_SALE_TYPE;

    /**
     * @var SaleTypeRepository
     */
    protected $salesTypeRepo;

    /**
     * SaleTypeApiController constructor.
     *
     * @param SaleTypeRepository $salesTypeRepo
     */
    public function __construct(SaleTypeRepository $salesTypeRepo)
    {
        parent::__construct();

        $this->salesTypeRepo = $salesTypeRepo;
    }

    /**
     * @SWG\Get(
     *   path="/sales_type",
     *   summary="List sales_type",
     *   operationId="listSalesTypes",
     *   tags={"product"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of sales_type",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/SaleType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $sales_type = SaleType::scope()->withTrashed()->orderBy('updated_at', 'desc');

        return $this->listResponse($sales_type);
    }

    /**
     * @SWG\Get(
     *   path="/sales_type/{sales_type_id}",
     *   summary="Retrieve a sales type",
     *   operationId="getSalesType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="sales_type_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single sales type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/SaleType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param SaleTypeRequest $request
     * @return
     */
    public function show(SaleTypeRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/sales_type",
     *   summary="Create a sales type",
     *   operationId="createSalesType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/SaleType")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New sales type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/SaleType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param SaleTypeRequest $request
     * @return
     */
    public function store(SaleTypeRequest $request)
    {
        $salesType = $this->salesTypeRepo->save($request->input());

        return $this->itemResponse($salesType);
    }

    /**
     * @SWG\Put(
     *   path="/sales_type/{sales_type_id}",
     *   summary="Update a sales type",
     *   operationId="updateSalesType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="sales_type_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="product",
     *     @SWG\Schema(ref="#/definitions/SaleType")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated sales type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/SaleType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param SaleTypeRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(SaleTypeRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $salesType = $this->salesTypeRepo->save($data, $request->entity());

        return $this->itemResponse($salesType);
    }

    /**
     * @SWG\Delete(
     *   path="/sales_type/{sales_type_id}",
     *   summary="Delete a sales type",
     *   operationId="deleteSalesType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="sales_type_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted sales type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/SaleType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param SaleTypeRequest $request
     * @return
     */
    public function destroy(SaleTypeRequest $request)
    {
        $salesType = $request->entity();

        $this->salesTypeRepo->delete($salesType);

        return $this->itemResponse($salesType);
    }
}
