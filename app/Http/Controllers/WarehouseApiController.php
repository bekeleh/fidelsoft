<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Models\Warehouse;
use App\Ninja\Repositories\WarehouseRepository;

/**
 * Class WarehouseApiController.
 */
class WarehouseApiController extends BaseAPIController
{

    protected $entityType = ENTITY_WAREHOUSE;
    protected $warehouseRepo;

    public function __construct(WarehouseRepository $warehouseRepo)
    {
        parent::__construct();

        $this->warehouseRepo = $warehouseRepo;
    }

    /**
     * @SWG\Get(
     *   path="/warehouses",
     *   summary="List warehouses",
     *   operationId="listWarehouses",
     *   tags={"store"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of warehouses",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Warehouse"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $stores = Warehouse::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($stores);
    }

    /**
     * @SWG\Get(
     *   path="/warehouses/{store_id}",
     *   summary="Retrieve a store",
     *   operationId="getWarehouse",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="store_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param WarehouseRequest $request
     * @return
     */
    public function show(WarehouseRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/warehouses",
     *   summary="Create a store",
     *   operationId="createWarehouse",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Warehouse")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param WarehouseRequest $request
     * @return
     */
    public function store(WarehouseRequest $request)
    {
        $store = $this->warehouseRepo->save($request->input());

        return $this->itemResponse($store);
    }

    /**
     * @SWG\Put(
     *   path="/warehouses/{store_id}",
     *   summary="Update a store",
     *   operationId="updateWarehouse",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="store_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="store",
     *     @SWG\Schema(ref="#/definitions/Warehouse")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param WarehouseRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(WarehouseRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $store = $this->warehouseRepo->save($data, $request->entity());

        return $this->itemResponse($store);
    }

    /**
     * @SWG\Delete(
     *   path="/warehouses/{store_id}",
     *   summary="Delete a store",
     *   operationId="deleteWarehouse",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="store_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param WarehouseRequest $request
     * @return
     */
    public function destroy(WarehouseRequest $request)
    {
        $store = $request->entity();

        $this->warehouseRepo->delete($store);

        return $this->itemResponse($store);
    }
}
