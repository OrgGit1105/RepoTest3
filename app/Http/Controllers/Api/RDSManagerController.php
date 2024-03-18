<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RDSManagerRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\Contracts\RDSManagerRepositoryInterface;
use Illuminate\Http\Request;

class RDSManagerController extends Controller
{
    /**
     * var Repository
     */
    protected $repository;

    public function __construct(RDSManagerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/rds_manager",
     *   tags={"RDSManager"},
     *   summary="List rds manager",
     *   operationId="rds_manager_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="page",
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
    public function index(RDSManagerRequest $request)
    {
        $data = $this->repository->get();
        return $this->responseJson(200, BaseResource::collection($data));
    }

    /**
     * @OA\Get(
     *   path="/api/rds_manager/{id}",
     *   tags={"RDSManager"},
     *   summary="Detail RDSManager",
     *   operationId="rds_manager_show",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
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
    public function show($id)
    {
        try {
            $data = $this->repository->find($id);
            return $this->responseJson(200, new BaseResource($data));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Post(
     *   path="/api/rds_manager",
     *   tags={"RDSManager"},
     *   summary="Add new rds_manager",
     *   operationId="rds_manager_create",
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *        mediaType="multipart/form-data",
     *        @OA\Schema(
     *          required={"name", "url_end_point", "username", "password", "port"},
     *            @OA\Property(
     *                property="name",
     *                format="string",
     *                example="Server240"
     *             ),
     *            @OA\Property(
     *                property="url_end_point",
     *                format="string",
     *                example=""
     *            ),
     *           @OA\Property(
     *                property="username",
     *                example="root",
     *                format="string",
     *           ),
     *           @OA\Property(
     *                property="password",
     *                format="string",
     *            ),
     *           @OA\Property(
     *                property="port",
     *                example="3306",
     *                format="string",
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
    public function store(RDSManagerRequest $request)
    {
        return $this->repository->create($request->all());
    }

    /**
     * @OA\Put(
     *   path="/api/rds_manager/{id}",
     *   tags={"RDSManager"},
     *   summary="Update RDSManager",
     *   operationId="rds_manager_update",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *            required={"name", "url_end_point", "username", "password", "port"},
     *            @OA\Property(
     *                property="name",
     *                format="string",
     *                example="Server240"
     *             ),
     *            @OA\Property(
     *                property="url_end_point",
     *                format="string",
     *                example=""
     *            ),
     *           @OA\Property(
     *                property="username",
     *                example="root",
     *                format="string",
     *           ),
     *           @OA\Property(
     *                property="password",
     *                format="string",
     *            ),
     *           @OA\Property(
     *                property="port",
     *                example="3306",
     *                format="string",
     *           ),
     *         ),
     *      )
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
    public function update(RDSManagerRequest $request, $id)
    {
        return $this->repository->update($request->except([]), $id);
    }

    /**
     * @OA\Delete(
     *   path="/api/rds_manager/{id}",
     *   tags={"RDSManager"},
     *   summary="Delete ..............",
     *   operationId="rds_manager_delete",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
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
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        return $this->repository->delete($id);
    }
}
