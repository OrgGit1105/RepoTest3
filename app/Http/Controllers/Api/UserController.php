<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Http\Controllers\Api;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use Aws\Iam\IamClient;
use Carbon\Carbon;
use Helper\Common;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * var Repository
     */
    protected $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/user_list_all",
     *   tags={"User"},
     *   summary="List all employee",
     *   operationId="all_employee",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
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
    public function getAllEmployee()
    {
        $data = $this->repository->getAll();
        return $this->responseJson(CODE_SUCCESS, BaseResource::collection($data));
    }

    /**
     * @OA\Get(
     *   path="/api/user",
     *   tags={"User"},
     *   summary="List user",
     *   operationId="user_index",
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
    public function index(UserRequest $request)
    {
        $data = $this->repository->pagination($request);
        foreach ($data as $item) {
            $item['retired'] = '';
            if ($item->retirement_date && Carbon::parse($item->retirement_date) <= Carbon::now()) {
                $item['retired'] = 'Retired';
            }
        }
        return $this->responseJson(200, BaseResource::collection($data));
    }

    /**
     * @OA\Post(
     *   path="/api/user",
     *   tags={"User"},
     *   summary="Add new user",
     *   operationId="user_create",
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *            required={"name", "email","viam_user_id", "password","password_confirmation"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="email",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="gender",
     *              format="integer",
     *              description="0:male, 1:female",
     *              enum={0,1},
     *            ),
     *            @OA\Property(
     *              property="birthday",
     *              type="date",
     *              description="YYYY-mm-dd",
     *            ),
     *            @OA\Property(
     *              property="address",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="telephone",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="entry_date",
     *              type="date",
     *              description="YYYY-mm-dd",
     *            ),
     *            @OA\Property(
     *              property="paid_off_start",
     *              type="float",
     *            ),
     *            @OA\Property(
     *              property="slack_id",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="skype_id",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="github_id",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="viam_user_id",
     *              format="integer",
     *            ),
     *            @OA\Property(
     *              property="ssh_public_key",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="status",
     *              format="integer",
     *              example=1,
     *            ),
     *            @OA\Property(
     *              property="password",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="password_confirmation",
     *              format="string",
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id":6,"name":"manager","email":"manager@gmail.com","password":123,"viam_user_id":1,"jwt_active":null,"retirement_date":null,"status":1,"created_at":1686191465,"updated_at":1686192839,"deleted_at":null}}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(UserRequest $request)
    {
        try {
            return $this->repository->create($request->all());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *   path="/api/user/{id}",
     *   tags={"User"},
     *   summary="Detail User",
     *   operationId="user_show",
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
     *      example={"code":200,"data":{"id":6,"name":"manager","email":"manager@gmail.com","password":123,"viam_user_id":1,"jwt_active":null,"retirement_date":null,"status":1,"created_at":1686191465,"updated_at":1686192839,"deleted_at":null}}
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
            $data = $this->repository->detail($id);
            if ($data) {
                return $this->responseJson(200, new BaseResource($data));
            }
            return $this->responseJsonError(CODE_NO_ACCESS, 'user not permission', 'user not permission');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\PUT(
     *   path="/api/user/{id}",
     *   tags={"User"},
     *   summary="Update User",
     *   operationId="user_update",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          example={"name":"string", "email": "string", "viam_user_id": "string", "ssh_public_key": "string", "password": "string", "password_confirmation": "string","retirement_date": "string"},
     *          @OA\Schema(
     *            required={"name", "email","viam_user_id","password","password_confirmation"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="email",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="viam_user_id",
     *              format="integer",
     *            ),
     *            @OA\Property(
     *              property="ssh_public_key",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="password",
     *              format="integer",
     *            ),
     *            @OA\Property(
     *              property="password_confirmation",
     *              format="integer",
     *            ),
     *         )
     *      )
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
    public function update(UserRequest $request, $id)
    {
        if ($request->has('password')) {
            $request->validate(['password' => 'nullable|min:3|confirmed']);
        }
        $attributes = $request->except(['paid_off', 'paid_off_start']);
//        $data = $this->repository->update($attributes, $id);
        return $this->repository->update($attributes, $id);
    }

    /**
     * @OA\Delete(
     *   path="/api/user/{id}",
     *   tags={"User"},
     *   summary="Delete ..............",
     *   operationId="user_delete",
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
        return $this->repository->delete($id);
    }

    /**
     * @OA\Post(
     *   path="/api/user/import",
     *   tags={"User"},
     *   summary="Import ..............",
     *   operationId="user_import",
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
    public function import()
    {

    }
}
