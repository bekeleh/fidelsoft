<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientTypeRequest;
use App\Models\ClientType;
use App\Ninja\Repositories\ClientTypeRepository;

/**
 * Class ClientTypeApiController.
 */
class ClientTypeApiController extends BaseAPIController
{
    protected $entityType = ENTITY_CLIENT_TYPE;


    protected $ClientTypeRepo;


    public function __construct(ClientTypeRepository $ClientTypeRepo)
    {
        parent::__construct();

        $this->ClientTypeRepo = $ClientTypeRepo;
    }

    /**
     * @SWG\Get(
     *   path="/client_type",
     *   summary="List client_type",
     *   operationId="listClientTypes",
     *   tags={"product"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of client_type",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ClientType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $client_type = ClientType::scope()->withTrashed()->orderBy('updated_at', 'desc');

        return $this->listResponse($client_type);
    }

    /**
     * @SWG\Get(
     *   path="/client_type/{client_type_id}",
     *   summary="Retrieve a client type",
     *   operationId="getClientType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="client_type_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ClientType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ClientTypeRequest $request
     * @return
     */
    public function show(ClientTypeRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/client_type",
     *   summary="Create a client type",
     *   operationId="createClientType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/ClientType")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ClientType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ClientTypeRequest $request
     * @return
     */
    public function store(ClientTypeRequest $request)
    {
        $clientType = $this->ClientTypeRepo->save($request->input());

        return $this->itemResponse($clientType);
    }

    /**
     * @SWG\Put(
     *   path="/client_type/{client_type_id}",
     *   summary="Update a client type",
     *   operationId="updateClientType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="client_type_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="product",
     *     @SWG\Schema(ref="#/definitions/ClientType")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ClientType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param ClientTypeRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ClientTypeRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $clientType = $this->ClientTypeRepo->save($data, $request->entity());

        return $this->itemResponse($clientType);
    }

    /**
     * @SWG\Delete(
     *   path="/client_type/{client_type_id}",
     *   summary="Delete a client type",
     *   operationId="deleteClientType",
     *   tags={"product"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="client_type_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted client type",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ClientType"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ClientTypeRequest $request
     * @return
     */
    public function destroy(ClientTypeRequest $request)
    {
        $clientType = $request->entity();

        $this->ClientTypeRepo->delete($clientType);

        return $this->itemResponse($clientType);
    }
}
