<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Ninja\Repositories\LocationRepository;

/**
 * Class LocationApiController.
 */
class LocationApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_LOCATION;

    /**
     * @var LocationRepository
     */
    protected $locationRepo;

    /**
     * LocationApiController constructor.
     *
     * @param LocationRepository $locationRepo
     */
    public function __construct(LocationRepository $locationRepo)
    {
        parent::__construct();

        $this->locationRepo = $locationRepo;
    }

    /**
     * @SWG\Get(
     *   path="/locations",
     *   summary="List locations",
     *   operationId="listLocations",
     *   tags={"location"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of locations",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Location"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $locations = Location::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($locations);
    }

    /**
     * @SWG\Get(
     *   path="/locations/{location_id}",
     *   summary="Retrieve a location",
     *   operationId="getLocation",
     *   tags={"location"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="location_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single location",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Location"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param LocationRequest $request
     * @return
     */
    public function show(LocationRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/locations",
     *   summary="Create a location",
     *   operationId="createLocation",
     *   tags={"location"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Location")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New location",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Location"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param LocationRequest $request
     * @return
     */
    public function store(LocationRequest $request)
    {
        $location = $this->locationRepo->save($request->input());

        return $this->itemResponse($location);
    }

    /**
     * @SWG\Put(
     *   path="/locations/{location_id}",
     *   summary="Update a location",
     *   operationId="updateLocation",
     *   tags={"location"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="location_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="location",
     *     @SWG\Schema(ref="#/definitions/Location")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated location",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Location"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param LocationRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(LocationRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $location = $this->locationRepo->save($data, $request->entity());

        return $this->itemResponse($location);
    }

    /**
     * @SWG\Delete(
     *   path="/locations/{location_id}",
     *   summary="Delete a location",
     *   operationId="deleteLocation",
     *   tags={"location"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="location_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted location",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Location"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param LocationRequest $request
     * @return
     */
    public function destroy(LocationRequest $request)
    {
        $location = $request->entity();

        $this->locationRepo->delete($location);

        return $this->itemResponse($location);
    }
}
