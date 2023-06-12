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
    public function index(ImageFaceRequest $request)
    {
        $data = $this->repository->findByField('user_id',$request->user_id);
        return $this->responseJson(200, BaseResource::collection($data));
    }

    /**
     * @OA\Post(
     *   path="/api/image_face",
     *   tags={"ImageFace"},
     *   summary="Create image_face",
     *   operationId="image_face_create",
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          example={"name":"string", "file": "string", "type": "string", "user_id": "integer"},
     *          @OA\Schema(
     *            required={"name", "file","type","user_id"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="file",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="type",
     *              format="string",
     *            ),
     *            @OA\Property(
     *              property="user_id",
     *              format="integer",
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id":6,"name":"manager","email":"manager@gmail.com","password":123,"role_id":1,"jwt_active":null,"retirement_date":null,"status":1,"created_at":1686191465,"updated_at":1686192839,"deleted_at":null}}
     *     )
     *   ),
     *   security={},
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(ImageFaceRequest $request)
    {
        try {
//          $data = $this->repository->createImageFace($request->all());
          return $this->repository->createImageFace($request->all());
//          return $this->responseJson(200, new BaseResource($data));
        } catch (\Exception $e) {
          throw $e;
        }
    }

    /**
     * @OA\Get(
     *   path="/api/image_face/{id}",
     *   tags={"ImageFace"},
     *   summary="Detail image_face",
     *   operationId="image_face_detail",
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
      public function detail($id){
        try {
          $data = $this->repository->find($id);
          return $this->responseJson(200, new BaseResource($data));
        } catch (\Exception $e) {
          throw $e;
        }
      }

    /**
     * @OA\Delete(
     *   path="/api/image_face/{id}",
     *   tags={"ImageFace"},
     *   summary="Delete ..............",
     *   operationId="image_face_delete",
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
      $this->repository->deleteImageFace($id);
      return $this->responseJson(200, null, trans('messages.mes.delete_success'));
    }
}
