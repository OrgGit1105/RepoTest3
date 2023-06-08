<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageFaceRequest;
use App\Repositories\Contracts\ImageFaceRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\ImageFaceResource;
use Illuminate\Http\Request;

class ImageFaceController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(ImageFaceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/image_face",
     *   tags={"ImageFace"},
     *   summary="List image_face",
     *   operationId="image_face_index",
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
    public function index(ImageFaceRequest $request)
    {
        $data = $this->repository->paginate($request->per_page);
        return $this->responseJson(200, BaseResource::collection($data));
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
     *      example={"code":200,"data":{"id":6,"name":"manager","email":"manager@gmail.com","password":123,"role_id":1,"jwt_active":null,"retirement_date":null,"status":1,"created_at":1686191465,"updated_at":1686192839,"deleted_at":null}}
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
    public function create(ImageFaceRequest $request,$id)
    {
        try {
          $data = $this->repository->createImageFace($request->all(),$id);
          return $this->responseJson(200, new BaseResource($data));
        } catch (\Exception $e) {
          throw $e;
        }
    }

}
