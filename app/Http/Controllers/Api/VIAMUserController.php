<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmotionRequest;
use App\Http\Requests\VIAMUserRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\Contracts\VIAMUserRepositoryInterface;
use Illuminate\Http\Request;

class VIAMUserController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(VIAMUserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/viam_user",
     *   tags={"VIamUser"},
     *   summary="List viam_user",
     *   operationId="viam_user_index",
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
    public function index(VIAMUserRequest $request)
    {
        $data = $this->repository->list($request->all());
        return $this->responseJson(CODE_SUCCESS, BaseResource::collection($data));
    }

    /**
     * @OA\Post(
     *   path="/api/viam_user",
     *   tags={"VIamUser"},
     *   summary="Add new viam_user",
     *   operationId="viam_user_create",
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *        mediaType="application/json",
     *        @OA\Schema(
     *          required={"name", "policy_id"},
     *            @OA\Property(
     *                property="name",
     *                format="string",
     *                example="VIAM_USER 1"
     *             ),
     *            @OA\Property(
     *                property="policy_id",
     *                format="string",
     *                example="[1,3]"
     *            ),
     *          @OA\Property(
     *                property="description",
     *                example="description",
     *                format="string",
     *             ),
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
    public function store(VIAMUserRequest $request)
    {
        try {
            return $this->repository->create($request->all());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *   path="/api/viam_user/{id}",
     *   tags={"VIamUser"},
     *   summary="Detail viam_user",
     *   operationId="viam_user_show",
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
            $data = $this->repository->with('policies')->find($id);
            return $this->responseJson(CODE_SUCCESS, new BaseResource($data));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Put(
     *   path="/api/viam_user/{id}",
     *   tags={"VIamUser"},
     *   summary="Update viam_user",
     *   operationId="viam_user_update",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *        mediaType="application/json",
     *        @OA\Schema(
     *          required={"name", "policy_id"},
     *            @OA\Property(
     *                property="name",
     *                format="string",
     *                example="VIAM_USER 1"
     *             ),
     *            @OA\Property(
     *                property="policy_id",
     *                format="string",
     *                example="[1,3]"
     *            ),
     *          @OA\Property(
     *                property="description",
     *                example="description",
     *                format="string",
     *             ),
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
    public function update(VIAMUserRequest $request, $id)
    {
        try {
            return $this->repository->update($request->all(), $id);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/viam_user/{id}",
     *   tags={"VIamUser"},
     *   summary="Delete viam_user",
     *   operationId="viam_user_delete",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *     @OA\Schema(
     *      type="string",
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
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
