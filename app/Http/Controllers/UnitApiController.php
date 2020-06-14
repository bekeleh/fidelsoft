<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitRequest;
use App\Models\Unit;
use App\Ninja\Repositories\UnitRepository;

/**
 * Class UnitApiController.
 */
class UnitApiController extends BaseAPIController
{

    protected $entityType = ENTITY_UNIT;

    protected $unitRepo;

    public function __construct(UnitRepository $unitRepo)
    {
        parent::__construct();

        $this->unitRepo = $unitRepo;
    }

    /**
     * @SWG\Get(
     *   path="/units",
     *   summary="List units",
     *   operationId="listUnits",
     *   tags={"unit"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of units",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Unit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $units = Unit::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($units);
    }

    /**
     * @SWG\Get(
     *   path="/units/{unit_id}",
     *   summary="Retrieve a unit",
     *   operationId="getUnit",
     *   tags={"unit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="unit_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single unit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Unit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UnitRequest $request
     * @return
     */
    public function show(UnitRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/units",
     *   summary="Create a unit",
     *   operationId="createUnit",
     *   tags={"unit"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Unit")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New unit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Unit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UnitRequest $request
     * @return
     */
    public function store(UnitRequest $request)
    {
        $unit = $this->unitRepo->save($request->input());

        return $this->itemResponse($unit);
    }

    /**
     * @SWG\Put(
     *   path="/units/{unit_id}",
     *   summary="Update a unit",
     *   operationId="updateUnit",
     *   tags={"unit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="unit_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="unit",
     *     @SWG\Schema(ref="#/definitions/Unit")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated unit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Unit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UnitRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UnitRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $unit = $this->unitRepo->save($data, $request->entity());

        return $this->itemResponse($unit);
    }

    /**
     * @SWG\Delete(
     *   path="/units/{unit_id}",
     *   summary="Delete a unit",
     *   operationId="deleteUnit",
     *   tags={"unit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="unit_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted unit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Unit"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UnitRequest $request
     * @return
     */
    public function destroy(UnitRequest $request)
    {
        $unit = $request->entity();

        $this->unitRepo->delete($unit);

        return $this->itemResponse($unit);
    }
}
