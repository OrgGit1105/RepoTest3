<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VIAMRDSRequest;
use App\Http\Requests\VIAMUserRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\ViamRDSResource;
use App\Repositories\Contracts\VIAMRDSRepositoryInterface;
use Illuminate\Http\Request;

class VIAMRDSController extends Controller
{
    /**
     * var Repository
     */
    protected $repository;

    public function __construct(VIAMRDSRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/viam_rds",
     *   tags={"VIamRDS"},
     *   summary="List viam_rds",
     *   operationId="viam_rds_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="rds_manager_id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="database_name",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(VIAMRDSRequest $request)
    {
        $data = $this->repository->list($request->all());
        return $this->responseJson(CODE_SUCCESS, new ViamRDSResource($data));
    }

    /**
     * @OA\Get(
     *   path="/api/viam_rds/detail-permission",
     *   tags={"VIamRDS"},
     *   summary="Detail viam_rds permission",
     *   operationId="viam_rds_detail_permission",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="rds_manager_id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="database_name",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(VIAMRDSRequest $request)
    {
        $data = $this->repository->permissionDetail($request->all());
        return $this->responseJson(CODE_SUCCESS, new ViamRDSResource($data));
    }

    /**
     * @OA\Get(
     *   path="/api/viam_rds/{rds_manager_id}",
     *   tags={"VIamRDS"},
     *   summary="List Database of RDS",
     *   operationId="list_database_rds",
     *   @OA\Parameter(
     *     name="rds_manager_id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name":"......"}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listDatabase(VIAMRDSRequest $request)
    {
        try {
            return $this->repository->getListDatabase($request->rds_manager_id);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @OA\Post(
     *   path="/api/viam_rds",
     *   tags={"VIamRDS"},
     *   summary="Add new viam_rds",
     *   operationId="viam_rds_create",
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *        mediaType="application/json",
     *        @OA\Schema(
     *          required={"rds_manager_id", "database_name", "user_id", "permission"},
     *            @OA\Property(
     *                property="rds_manager_id",
     *                type="integer",
     *                example="1"
     *             ),
     *            @OA\Property(
     *                property="database_name",
     *                type="string",
     *                example="cck"
     *            ),
     *           @OA\Property(
     *                property="user_id",
     *                example="1",
     *                type="integer",
     *           ),
     *           @OA\Property(
     *               property="permission",
     *               format="object",
     *               example={1,2}
     *           ),
     *         ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name": "......"}}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(VIAMRDSRequest $request)
    {
        try {
            return $this->repository->create($request->all());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Put(
     *   path="/api/viam_rds/{user_id}",
     *   tags={"VIamRDS"},
     *   summary="Update viam_rds",
     *   operationId="viam_rds_update",
     *   @OA\Parameter(
     *     name="user_id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *        mediaType="application/json",
     *        @OA\Schema(
     *          required={"rds_manager_id", "database_id", "permission"},
     *            @OA\Property(
     *                property="rds_manager_id",
     *                type="integer",
     *                example="1"
     *             ),
     *            @OA\Property(
     *                property="database_id",
     *                type="integer",
     *                example=1
     *            ),
     *           @OA\Property(
     *               property="permission",
     *               type="array",
     *               items={"type":"integer", "example":1}
     *           ),
     *         ),
     *       ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name":  "............."}}
     *     ),
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Access Deny permission",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Access Deny permission"}
     *     ),
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VIAMRDSRequest $request, $id)
    {
        try {
            return $this->repository->update($request->all(), $id);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/viam_rds/{user_id}",
     *   tags={"VIamRDS"},
     *   summary="Delete viam_rds",
     *   operationId="viam_rds_delete",
     *   @OA\Parameter(
     *      name="user_id",
     *      in="path",
     *      required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *      name="rds_manager_id",
     *      in="query",
     *      required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *      name="database_id",
     *      in="query",
     *      required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":"Send request success"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @param int $user_id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(VIAMRDSRequest $request, $user_id)
    {
        try {
            return $this->repository->deleteRDS($request->all(), $user_id);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
