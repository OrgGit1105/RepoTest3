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
use Illuminate\Support\Facades\Storage;

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
     *              description="in WithoutMask, WithMask"
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
     *      example={"code":200,"data":{{"type":"WithoutMask","user_id":"1","file":"WithoutMask\/1687149746BachImage.jpg","created_at":"2023-06-19T04:42:24.695193Z","face_rekognition_id":"6fb9401c-677b-47c5-8bf6-f80a3c092047"}}}
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
      return $this->repository->deleteImageFace($id);
    }

  /**
   * @OA\Post(
   *   path="/api/image_face/compareFace",
   *   tags={"ImageFace"},
   *   summary="Compare image_face",
   *   operationId="image_face_compare",
   *   @OA\RequestBody(
   *       @OA\MediaType(
   *          mediaType="multipart/form-data",
   *          example={"file":"file|string", "time": "string"},
   *          @OA\Schema(
   *            required={"file","time"},
   *            @OA\Property(
   *              property="file",
   *              description="The file or string base64",
   *              type="file",
   *            ),
   *            @OA\Property(
   *              property="time",
   *              format="string",
   *              description="in, out",
   *            ),
   *         )
   *      )
   *   ),
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id":7,"file":"WithoutMask\/1687151741BachImage.jpg","user_id":1,"type":"WithoutMask","created_at":"2023-06-19 12:15:38","updated_at":null,"deleted_at":null,"face_rekognition_id":"2ad7c68b-68cb-4c55-a0d4-b2ca39b3c65b"}}
   *     )
   *   ),
   * )
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
    public function compareFace(ImageFaceRequest $request){
      return $this->repository->compareFace($request->all());
    }

  /**
   * @OA\Post(
   *   path="/api/image_face/checkImage",
   *   tags={"ImageFace"},
   *   summary="check image_face",
   *   operationId="image_face_check_image",
   *     	@OA\RequestBody(
   *          required=true,
   *          @OA\MediaType(
   *              mediaType="multipart/form-data",
   *              @OA\Schema(
   *                  @OA\Property(
   *                      property="file",
   *                      description="file",
   *                      type="file",
   *                   ),
   *               ),
   *           ),
   *       ),
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"id":7,"file":"WithoutMask\/1687151741BachImage.jpg","user_id":1,"type":"WithoutMask","created_at":"2023-06-19 12:15:38","updated_at":null,"deleted_at":null,"face_rekognition_id":"2ad7c68b-68cb-4c55-a0d4-b2ca39b3c65b"}}
   *     )
   *   ),
   * )
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function checkImage(ImageFaceRequest $request){
    return $this->repository->checkImage($request->all());
  }

  /**
   * @OA\Get(
   *   path="/api/checkIpAddress",
   *   tags={"ImageFace"},
   *   summary="check image_face ip_address",
   *   operationId="image_face_check_ip_address",
   *   @OA\Response(
   *     response=200,
   *     description="Send request success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"data":{"ip":"127.0.0.1"}}
   *     )
   *   ),
   * )
   * @param int $id
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function checkIpAddress(Request $request){

    return $this->responseJson(200, new BaseResource([
      "ipAddress" => $request->ip(),
      "getHost" => $request->getHost(),
      "getClientIp" => $request->getClientIp(),
      "getHttpHost" => $request->getHttpHost(),
      "userAgent" => $request->userAgent(),
    ]));
  }

//    public function getAllImageAWS(Request $request){
//      $images = [];
//      $files = Storage::disk('s3')->files($request->type);
//      foreach ($files as $file) {
//        $images[] = [
//          'name' => str_replace('images/', '', $file),
//          'src' => config('services.aws.urlImage'). $file
//        ];
//      }
//      return $images;
//    }
  public function sendMailNegative(Request $request){

    return $this->repository->sendMailNegative();
  }

    /**
     * @OA\Post(
     *   path="/api/image_face/breakTime",
     *   tags={"ImageFace"},
     *   summary="break time manager",
     *   operationId="break_time_manager",
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *            required={"file","time"},
     *            @OA\Property(
     *              property="file",
     *              type="file",
     *            ),
     *            @OA\Property(
     *              property="time",
     *              format="string",
     *              enum={"go_out", "go_into"},
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id":7,"file":"WithoutMask\/1687151741BachImage.jpg","user_id":1,"type":"WithoutMask","created_at":"2023-06-19 12:15:38","updated_at":null,"deleted_at":null,"face_rekognition_id":"2ad7c68b-68cb-4c55-a0d4-b2ca39b3c65b"}}
     *     )
     *   ),
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function breakTime(ImageFaceRequest $request)
    {
        return $this->repository->breakTime($request->all());
    }
}
