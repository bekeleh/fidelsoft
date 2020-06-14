<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\DepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Ninja\Repositories\DepartmentRepository;

class DepartmentApiController extends BaseAPIController
{
    protected $departmentRepo;

    protected $entityType = ENTITY_DEPARTMENT;

    public function __construct(DepartmentRepository $departmentRepo)
    {
        parent::__construct();

        $this->departmentRepo = $departmentRepo;
    }

    /**
     * @SWG\Get(
     *   path="/departments",
     *   summary="List departments",
     *   operationId="listDepartments",
     *   tags={"credit"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of departments",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Department"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $departments = Department::Scope()
            ->withTrashed()
            ->orderBy('updated_at', 'desc');

        return $this->listResponse($departments);
    }

    /**
     * @SWG\Get(
     *   path="/departments/{department_id}",
     *   summary="Retrieve a credit",
     *   operationId="getDepartment",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="department_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Department"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param DepartmentRequest $request
     * @return
     */
    public function show(DepartmentRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/departments",
     *   summary="Create a credit",
     *   operationId="createDepartment",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="credit",
     *     @SWG\Schema(ref="#/definitions/Department")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Department"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param CreateDepartmentRequest $request
     * @return
     */
    public function store(CreateDepartmentRequest $request)
    {
        $department = $this->departmentRepo->save($request->input());

        return $this->itemResponse($department);
    }

    /**
     * @SWG\Put(
     *   path="/departments/{department_id}",
     *   summary="Update a credit",
     *   operationId="updateDepartment",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="department_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="credit",
     *     @SWG\Schema(ref="#/definitions/Department")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Department"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param UpdateDepartmentRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(UpdateDepartmentRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $department = $this->departmentRepo->save($data, $request->entity());

        return $this->itemResponse($department);
    }

    /**
     * @SWG\Delete(
     *   path="/departments/{department_id}",
     *   summary="Delete a credit",
     *   operationId="deleteDepartment",
     *   tags={"credit"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="department_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted credit",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Department"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param UpdateDepartmentRequest $request
     * @return
     */
    public function destroy(UpdateDepartmentRequest $request)
    {
        $department = $request->entity();

        $this->departmentRepo->delete($department);

        return $this->itemResponse($department);
    }
}
