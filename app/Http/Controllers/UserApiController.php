<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Ninja\Repositories\UserRepository;
use App\Services\UserService;

class UserApiController extends BaseAPIController
{
    protected $userService;
    protected $userRepo;

    protected $entityType = ENTITY_USER;

    public function __construct(UserService $userService, UserRepository $userRepo)
    {
        parent::__construct();

        $this->userService = $userService;
        $this->userRepo = $userRepo;
    }

    /**
     * @SWG\Get(
     *   path="/users",
     *   summary="List users",
     *   operationId="listUsers",
     *   tags={"user"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of users",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/User"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $this->authorize('index', $this->userRepo->getModel());

        $users = User::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($users);
    }

    /**
     * @SWG\Get(
     *   path="/users/{user_id}",
     *   summary="Retrieve a user",
     *   operationId="getUser",
     *   tags={"client"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="user_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/User"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UserRequest $request
     * @return
     */
    public function show(UserRequest $request)
    {
        $this->authorize('view', $this->userRepo->getModel());
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/users",
     *   summary="Create a user",
     *   operationId="createUser",
     *   tags={"user"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="user",
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/User"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UserRequest $request
     * @return
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', $this->userRepo->getModel());
        $unit = $this->userRepo->save($request->input());

        return $this->itemResponse($unit);
    }

    /**
     * @SWG\Put(
     *   path="/users/{user_id}",
     *   summary="Update a user",
     *   operationId="updateUser",
     *   tags={"user"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="user_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="user",
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/User"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UserRequest $request
     * @param $publicId
     * @return
     */
    public function update(UserRequest $request, $publicId)
    {
        $this->authorize('update', $this->userRepo->getModel());
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $unit = $this->userRepo->save($data, $request->entity());

        return $this->itemResponse($unit);
    }

    /**
     * @SWG\Delete(
     *   path="/users/{user_id}",
     *   summary="Delete a user",
     *   operationId="deleteUser",
     *   tags={"user"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="user_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/User"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UserRequest $request
     * @return
     */
    public function destroy(UserRequest $request)
    {
        $this->authorize('delete', $this->userRepo->getModel());
        $entity = $request->entity();

        $this->userRepo->delete($entity);

        return $this->itemResponse($entity);
    }
}
